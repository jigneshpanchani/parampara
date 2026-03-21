<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
use App\Models\SellInvoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SellInvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = SellInvoice::query()
            ->orderBy('invoice_date', 'desc')
            ->orderBy('invoice_type')
            ->paginate(20);

        return view('admin.sell-invoices.index', compact('invoices'));
    }

    public function create(Request $request): View
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $invoiceType = $request->input('invoice_type', SellInvoice::TYPE_CASH);
        if (! in_array($invoiceType, [SellInvoice::TYPE_CASH, SellInvoice::TYPE_ONLINE], true)) {
            $invoiceType = SellInvoice::TYPE_CASH;
        }

        $dateCarbon = Carbon::parse($date)->startOfDay();

        $pendingCash = $this->eligibleSellsQuery($dateCarbon, SellInvoice::TYPE_CASH)->count();
        $pendingOnline = $this->eligibleSellsQuery($dateCarbon, SellInvoice::TYPE_ONLINE)->count();

        $alreadyCash = SellInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SellInvoice::TYPE_CASH)
            ->exists();
        $alreadyOnline = SellInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SellInvoice::TYPE_ONLINE)
            ->exists();

        $alreadyInvoiced = $invoiceType === SellInvoice::TYPE_CASH ? $alreadyCash : $alreadyOnline;
        $pendingCount = $invoiceType === SellInvoice::TYPE_CASH ? $pendingCash : $pendingOnline;

        return view('admin.sell-invoices.create', compact(
            'date',
            'invoiceType',
            'pendingCount',
            'pendingCash',
            'pendingOnline',
            'alreadyInvoiced',
            'alreadyCash',
            'alreadyOnline'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_date' => 'required|date',
            'invoice_type' => 'required|in:cash,online',
            'notes' => 'nullable|string|max:2000',
        ]);

        $date = Carbon::parse($validated['invoice_date'])->startOfDay();
        $type = $validated['invoice_type'];

        if (SellInvoice::whereDate('invoice_date', $date)->where('invoice_type', $type)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'An invoice of this type already exists for this date. Delete it first if you need to regenerate.');
        }

        $sells = $this->eligibleSellsQuery($date, $type)->with('items.product')->orderBy('id')->get();

        if ($sells->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'No matching sales for this date and invoice type. (Cash invoice: cash & mix cash portion. Online invoice: UPI, G-Pay & mix online portion.)');
        }

        $agg = SellInvoice::aggregateTotalsFromSells($sells, $type);

        $invoice = DB::transaction(function () use ($validated, $date, $type, $sells, $agg) {
            $invoice = SellInvoice::create([
                'invoice_number' => SellInvoice::makeInvoiceNumber($date, $type),
                'invoice_date' => $date->format('Y-m-d'),
                'invoice_type' => $type,
                'total_amount' => $agg['total_amount'],
                'cash_total' => $agg['cash_total'],
                'online_total' => $agg['online_total'],
                'sells_count' => $agg['sells_count'],
                'notes' => $validated['notes'] ?? null,
            ]);

            $fk = $type === SellInvoice::TYPE_ONLINE ? 'online_sell_invoice_id' : 'cash_sell_invoice_id';
            Sell::whereIn('id', $sells->pluck('id'))->update([$fk => $invoice->id]);

            return $invoice;
        });

        return redirect()
            ->route('admin.sell-invoices.show', $invoice)
            ->with('success', $invoice->invoice_type_label . ' ' . $invoice->invoice_number . ' generated successfully.');
    }

    public function show(SellInvoice $sellInvoice): View
    {
        $sellInvoice->load(['sells' => function ($q) {
            $q->with('items.product')->orderBy('id');
        }]);

        return view('admin.sell-invoices.show', compact('sellInvoice'));
    }

    public function destroy(SellInvoice $sellInvoice): RedirectResponse
    {
        $sellInvoice->delete();

        return redirect()
            ->route('admin.sell-invoices.index')
            ->with('success', 'Invoice removed. Matching sales can be invoiced again for that type.');
    }

    public function export(SellInvoice $sellInvoice): StreamedResponse
    {
        $sellInvoice->load(['sells' => function ($q) {
            $q->with('items.product')->orderBy('id');
        }]);

        $fileName = $sellInvoice->invoice_number . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($sellInvoice) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $title = $sellInvoice->invoice_type === SellInvoice::TYPE_CASH
                ? 'Daily Sell Invoice — Cash'
                : 'Daily Sell Invoice — Online (UPI / G-Pay)';

            $sheet->setCellValue('A1', $title);
            $sheet->mergeCells('A1:K1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            $sheet->setCellValue('A2', 'Invoice No: ' . $sellInvoice->invoice_number);
            $sheet->mergeCells('A2:K2');

            $sheet->setCellValue('A3', 'Type: ' . $sellInvoice->invoice_type_label);
            $sheet->mergeCells('A3:K3');

            $sheet->setCellValue('A4', 'Invoice Date: ' . $sellInvoice->invoice_date->format('d M Y'));
            $sheet->mergeCells('A4:K4');

            $sheet->setCellValue('A5', 'Generated: ' . $sellInvoice->created_at->format('d M Y H:i'));
            $sheet->mergeCells('A5:K5');

            $summary = 'Summary: Total ₹' . number_format($sellInvoice->total_amount, 2)
                . ' | Cash ₹' . number_format($sellInvoice->cash_total, 2)
                . ' | Online ₹' . number_format($sellInvoice->online_total, 2)
                . ' | Sale rows: ' . $sellInvoice->sells_count;
            $sheet->setCellValue('A6', $summary);
            $sheet->mergeCells('A6:K6');

            $rowMeta = 7;
            if ($sellInvoice->notes) {
                $sheet->setCellValue('A7', 'Notes: ' . $sellInvoice->notes);
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
            $type = $sellInvoice->invoice_type;

            foreach ($sellInvoice->sells as $sell) {
                foreach ($sell->items as $item) {
                    $onInvoice = SellInvoice::lineAmountForInvoiceType($sell, $item, $type);
                    $sheet->setCellValueByColumnAndRow(1, $row, $n++);
                    $sheet->setCellValueByColumnAndRow(2, $row, $sell->id);
                    $sheet->setCellValueByColumnAndRow(3, $row, $sell->created_at?->format('H:i') ?? '');
                    $sheet->setCellValueByColumnAndRow(4, $row, $sell->seller_name ?? '-');
                    $sheet->setCellValueByColumnAndRow(5, $row, $item->product?->product_name ?? '—');
                    $sheet->setCellValueByColumnAndRow(6, $row, $item->quantity);
                    $sheet->setCellValueByColumnAndRow(7, $row, (float) $item->selling_price);
                    $sheet->setCellValueByColumnAndRow(8, $row, (float) $item->total_price);
                    $sheet->setCellValueByColumnAndRow(9, $row, (float) $onInvoice);
                    $sheet->setCellValueByColumnAndRow(10, $row, (string) ($sell->payment_mode_label ?? $sell->payment_mode));
                    $sheet->setCellValueByColumnAndRow(11, $row, (float) $sell->amount_paid);
                    $row++;
                }
            }

            $row++;
            $sheet->setCellValueByColumnAndRow(1, $row, 'Totals (invoice)');
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);
            $sheet->setCellValueByColumnAndRow(9, $row, (float) $sellInvoice->total_amount);

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
     * Sales eligible for this invoice type (not yet linked on that side).
     */
    protected function eligibleSellsQuery(Carbon $date, string $invoiceType): Builder
    {
        $q = Sell::query()->whereDate('sell_date', $date);

        if ($invoiceType === SellInvoice::TYPE_CASH) {
            return $q->whereNull('cash_sell_invoice_id')
                ->where(function (Builder $q) {
                    $q->where('payment_mode', 'cash')
                        ->orWhere(function (Builder $q) {
                            $q->where('payment_mode', 'mix')->where('cash_amount', '>', 0);
                        });
                });
        }

        return $q->whereNull('online_sell_invoice_id')
            ->where(function (Builder $q) {
                $q->whereIn('payment_mode', ['upi', 'gpay'])
                    ->orWhere(function (Builder $q) {
                        $q->where('payment_mode', 'mix')->where('online_amount', '>', 0);
                    });
            });
    }
}
