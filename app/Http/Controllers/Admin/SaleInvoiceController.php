<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SaleInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SaleInvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = SaleInvoice::query()
            ->orderBy('invoice_date', 'desc')
            ->orderBy('invoice_type')
            ->paginate(20);

        $invoiceIds = $invoices->pluck('id');
        $firstSaleByInvoice = [];
        if ($invoiceIds->isNotEmpty()) {
            $ids = $invoiceIds->all();
            $sales = Sale::query()
                ->where(function ($q) use ($ids) {
                    $q->whereIn('cash_sale_invoice_id', $ids)
                        ->orWhereIn('online_sale_invoice_id', $ids)
                        ->orWhereIn('mix_sale_invoice_id', $ids);
                })
                ->get(['id', 'cash_sale_invoice_id', 'online_sale_invoice_id', 'mix_sale_invoice_id']);

            foreach ($sales as $sale) {
                foreach (['cash_sale_invoice_id', 'online_sale_invoice_id', 'mix_sale_invoice_id'] as $fk) {
                    $iid = $sale->{$fk};
                    if ($iid !== null && in_array($iid, $ids, true)) {
                        if (! isset($firstSaleByInvoice[$iid]) || $sale->id < $firstSaleByInvoice[$iid]) {
                            $firstSaleByInvoice[$iid] = $sale->id;
                        }
                    }
                }
            }
        }

        return view('admin.sale-invoices.index', compact('invoices', 'firstSaleByInvoice'));
    }

    public function create(Request $request): View
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $invoiceType = $request->input('invoice_type', SaleInvoice::TYPE_CASH);
        if (! in_array($invoiceType, [SaleInvoice::TYPE_CASH, SaleInvoice::TYPE_ONLINE, SaleInvoice::TYPE_MIX], true)) {
            $invoiceType = SaleInvoice::TYPE_CASH;
        }

        $dateCarbon = Carbon::parse($date)->startOfDay();

        $pendingCash = $this->eligibleSalesQuery($dateCarbon, SaleInvoice::TYPE_CASH)->count();
        $pendingOnline = $this->eligibleSalesQuery($dateCarbon, SaleInvoice::TYPE_ONLINE)->count();
        $pendingMix = $this->eligibleSalesQuery($dateCarbon, SaleInvoice::TYPE_MIX)->count();

        $alreadyCash = SaleInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SaleInvoice::TYPE_CASH)
            ->exists();
        $alreadyOnline = SaleInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SaleInvoice::TYPE_ONLINE)
            ->exists();
        $alreadyMix = SaleInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SaleInvoice::TYPE_MIX)
            ->exists();

        $alreadyInvoiced = match ($invoiceType) {
            SaleInvoice::TYPE_ONLINE => $alreadyOnline,
            SaleInvoice::TYPE_MIX => $alreadyMix,
            default => $alreadyCash,
        };

        $pendingCount = match ($invoiceType) {
            SaleInvoice::TYPE_ONLINE => $pendingOnline,
            SaleInvoice::TYPE_MIX => $pendingMix,
            default => $pendingCash,
        };

        return view('admin.sale-invoices.create', compact(
            'date',
            'invoiceType',
            'pendingCount',
            'pendingCash',
            'pendingOnline',
            'pendingMix',
            'alreadyInvoiced',
            'alreadyCash',
            'alreadyOnline',
            'alreadyMix'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'invoice_type' => 'required|in:cash,online,mix',
            'notes' => 'nullable|string|max:2000',
        ]);

        $date = Carbon::parse($validated['invoice_date'])->startOfDay();
        $type = $validated['invoice_type'];

        if (SaleInvoice::whereDate('invoice_date', $date)->where('invoice_type', $type)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'An invoice of this type already exists for this date. Remove the existing one from the list first if you need to regenerate.');
        }

        $sales = $this->eligibleSalesQuery($date, $type)->with('items.product')->orderBy('id')->get();

        if ($sales->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'No matching sales for this date and invoice type. Cash: cash only. Online: UPI & G-Pay only. Mix: mix payments only.');
        }

        $agg = SaleInvoice::aggregateTotalsFromSales($sales, $type);

        $invoice = DB::transaction(function () use ($validated, $date, $type, $sales, $agg) {
            $invoice = SaleInvoice::create([
                'invoice_number' => SaleInvoice::makeInvoiceNumber($date, $type),
                'invoice_date' => $date->format('Y-m-d'),
                'invoice_type' => $type,
                'total_amount' => $agg['total_amount'],
                'cash_total' => $agg['cash_total'],
                'online_total' => $agg['online_total'],
                'sales_count' => $agg['sales_count'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $fk = match ($type) {
                SaleInvoice::TYPE_ONLINE => 'online_sale_invoice_id',
                SaleInvoice::TYPE_MIX => 'mix_sale_invoice_id',
                default => 'cash_sale_invoice_id',
            };
            Sale::whereIn('id', $sales->pluck('id'))->update([$fk => $invoice->id]);

            return $invoice;
        });

        return redirect()
            ->route('admin.sale-invoices.show', $invoice)
            ->with('success', $invoice->invoice_type_label . ' ' . $invoice->invoice_number . ' generated successfully.');
    }

    public function show(SaleInvoice $saleInvoice): View
    {
        $saleInvoice->loadSalesForDisplay();

        return view('admin.sale-invoices.show', compact('saleInvoice'));
    }

    /**
     * Soft-delete invoice and unlink sales (removes from list; data retained).
     */
    public function destroy(SaleInvoice $saleInvoice): RedirectResponse
    {
        DB::transaction(function () use ($saleInvoice) {
            $saleInvoice->unlinkSalesFromInvoice();
            $saleInvoice->delete();
        });

        return redirect()
            ->route('admin.sale-invoices.index')
            ->with('success', 'Invoice removed from the list. You can generate a new one for that date and type.');
    }

    public function export(SaleInvoice $saleInvoice): StreamedResponse
    {
        $saleInvoice->loadSalesForDisplay();

        $fileName = $saleInvoice->invoice_number . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($saleInvoice) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $title = match ($saleInvoice->invoice_type) {
                SaleInvoice::TYPE_CASH => 'Daily Sales Invoice — Cash',
                SaleInvoice::TYPE_ONLINE => 'Daily Sales Invoice — Online (UPI / G-Pay)',
                SaleInvoice::TYPE_MIX => 'Daily Sales Invoice — Mix (Cash + Online)',
                default => 'Daily Sales Invoice',
            };

            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $sheet->setCellValue('A2', 'Invoice No: ' . $saleInvoice->invoice_number);
            $sheet->mergeCells('A2:K2');

            $sheet->setCellValue('A3', 'Type: ' . $saleInvoice->invoice_type_label);
            $sheet->mergeCells('A3:K3');

            $sheet->setCellValue('A4', 'Invoice Date: ' . $saleInvoice->invoice_date->format('d M Y'));
            $sheet->mergeCells('A4:K4');

            $sheet->setCellValue('A5', 'Generated: ' . $saleInvoice->created_at->format('d M Y H:i'));
            $sheet->mergeCells('A5:K5');

            $summary = 'Summary: Total ₹' . number_format($saleInvoice->total_amount, 2)
                . ' | Cash ₹' . number_format($saleInvoice->cash_total, 2)
                . ' | Online ₹' . number_format($saleInvoice->online_total, 2)
                . ' | Sale rows: ' . $saleInvoice->sales_count;
            $sheet->setCellValue('A6', $summary);
            $sheet->mergeCells('A6:K6');

            $rowMeta = 7;
            if ($saleInvoice->notes) {
                $sheet->setCellValue('A7', 'Notes: ' . $saleInvoice->notes);
                $sheet->mergeCells('A7:K7');
                $rowMeta = 8;
            }

            $startRow = $rowMeta + 1;
            $headers = ['#', 'Sale ID', 'Time', 'Seller', 'Product', 'Qty', 'Price', 'Line total', 'On invoice', 'Payment', 'Paid'];
            $col = 1;
            foreach ($headers as $h) {
                $sheet->setCellValueByColumnAndRow($col, $startRow, $h);
                $sheet->getStyleByColumnAndRow($col, $startRow)->getFont()->setBold(true);
                $col++;
            }

            $row = $startRow + 1;
            $n = 1;
            $type = $saleInvoice->invoice_type;

            foreach ($saleInvoice->sales as $sale) {
                foreach ($sale->items as $item) {
                    $onInvoice = SaleInvoice::lineAmountForInvoiceType($sale, $item, $type);
                    $sheet->setCellValueByColumnAndRow(1, $row, $n++);
                    $sheet->setCellValueByColumnAndRow(2, $row, $sale->id);
                    $sheet->setCellValueByColumnAndRow(3, $row, $sale->created_at?->format('H:i') ?? '');
                    $sheet->setCellValueByColumnAndRow(4, $row, $sale->seller_name ?? '-');
                    $sheet->setCellValueByColumnAndRow(5, $row, $item->product?->product_name ?? '—');
                    $sheet->setCellValueByColumnAndRow(6, $row, $item->quantity);
                    $sheet->setCellValueByColumnAndRow(7, $row, (float) $item->selling_price);
                    $sheet->setCellValueByColumnAndRow(8, $row, (float) $item->total_price);
                    $sheet->setCellValueByColumnAndRow(9, $row, (float) $onInvoice);
                    $sheet->setCellValueByColumnAndRow(10, $row, (string) ($sale->payment_mode_label ?? $sale->payment_mode));
                    $sheet->setCellValueByColumnAndRow(11, $row, (float) $sale->total_paid);
                    $row++;
                }
            }

            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, 'Totals (invoice)');
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(9, $row, (float) $saleInvoice->total_amount);

            for ($i = 1; $i <= 11; $i++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Sales eligible for this invoice type (not yet linked on that invoice column).
     */
    protected function eligibleSalesQuery(Carbon $date, string $invoiceType): Builder
    {
        $q = Sale::query()->whereDate('sale_date', $date);

        return match ($invoiceType) {
            SaleInvoice::TYPE_CASH => $q->whereNull('cash_sale_invoice_id')->where('payment_mode', 'cash'),
            SaleInvoice::TYPE_ONLINE => $q->whereNull('online_sale_invoice_id')->whereIn('payment_mode', ['upi', 'gpay']),
            SaleInvoice::TYPE_MIX => $q->whereNull('mix_sale_invoice_id')->where('payment_mode', 'mix'),
            default => $q->whereRaw('1 = 0'),
        };
    }
}
