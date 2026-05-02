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

        $invoiceIds = $invoices->pluck('id');
        $firstSellByInvoice = [];
        if ($invoiceIds->isNotEmpty()) {
            $ids = $invoiceIds->all();
            $sells = Sell::query()
                ->where(function ($q) use ($ids) {
                    $q->whereIn('cash_sell_invoice_id', $ids)
                        ->orWhereIn('online_sell_invoice_id', $ids)
                        ->orWhereIn('mix_sell_invoice_id', $ids);
                })
                ->get(['id', 'cash_sell_invoice_id', 'online_sell_invoice_id', 'mix_sell_invoice_id']);

            foreach ($sells as $sell) {
                foreach (['cash_sell_invoice_id', 'online_sell_invoice_id', 'mix_sell_invoice_id'] as $fk) {
                    $iid = $sell->{$fk};
                    if ($iid !== null && in_array($iid, $ids, true)) {
                        if (! isset($firstSellByInvoice[$iid]) || $sell->id < $firstSellByInvoice[$iid]) {
                            $firstSellByInvoice[$iid] = $sell->id;
                        }
                    }
                }
            }
        }

        return view('admin.sell-invoices.index', compact('invoices', 'firstSellByInvoice'));
    }

    public function create(Request $request): View
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $invoiceType = $request->input('invoice_type', SellInvoice::TYPE_CASH);
        if (! in_array($invoiceType, [SellInvoice::TYPE_CASH, SellInvoice::TYPE_ONLINE, SellInvoice::TYPE_MIX], true)) {
            $invoiceType = SellInvoice::TYPE_CASH;
        }

        $dateCarbon = Carbon::parse($date)->startOfDay();

        $pendingCash = $this->eligibleSellsQuery($dateCarbon, SellInvoice::TYPE_CASH)->count();
        $pendingOnline = $this->eligibleSellsQuery($dateCarbon, SellInvoice::TYPE_ONLINE)->count();
        $pendingMix = $this->eligibleSellsQuery($dateCarbon, SellInvoice::TYPE_MIX)->count();

        $alreadyCash = SellInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SellInvoice::TYPE_CASH)
            ->exists();
        $alreadyOnline = SellInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SellInvoice::TYPE_ONLINE)
            ->exists();
        $alreadyMix = SellInvoice::whereDate('invoice_date', $dateCarbon)
            ->where('invoice_type', SellInvoice::TYPE_MIX)
            ->exists();

        $alreadyInvoiced = match ($invoiceType) {
            SellInvoice::TYPE_ONLINE => $alreadyOnline,
            SellInvoice::TYPE_MIX => $alreadyMix,
            default => $alreadyCash,
        };

        $pendingCount = match ($invoiceType) {
            SellInvoice::TYPE_ONLINE => $pendingOnline,
            SellInvoice::TYPE_MIX => $pendingMix,
            default => $pendingCash,
        };

        return view('admin.sell-invoices.create', compact(
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

        if (SellInvoice::whereDate('invoice_date', $date)->where('invoice_type', $type)->exists()) {
            return back()
                ->withInput()
                ->with('error', 'An invoice of this type already exists for this date. Remove the existing one from the list first if you need to regenerate.');
        }

        $sells = $this->eligibleSellsQuery($date, $type)->with('items.product')->orderBy('id')->get();

        if ($sells->isEmpty()) {
            return back()
                ->withInput()
                ->with('error', 'No matching sales for this date and invoice type. Cash: cash only. Online: UPI & G-Pay only. Mix: mix payments only.');
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

            $fk = match ($type) {
                SellInvoice::TYPE_ONLINE => 'online_sell_invoice_id',
                SellInvoice::TYPE_MIX => 'mix_sell_invoice_id',
                default => 'cash_sell_invoice_id',
            };
            Sell::whereIn('id', $sells->pluck('id'))->update([$fk => $invoice->id]);

            return $invoice;
        });

        return redirect()
            ->route('admin.sell-invoices.show', $invoice)
            ->with('success', $invoice->invoice_type_label . ' ' . $invoice->invoice_number . ' generated successfully.');
    }

    public function show(SellInvoice $sellInvoice): View
    {
        $sellInvoice->loadSellsForDisplay();

        return view('admin.sell-invoices.show', compact('sellInvoice'));
    }

    /**
     * Soft-delete invoice and unlink sales (removes from list; data retained).
     */
    public function destroy(SellInvoice $sellInvoice): RedirectResponse
    {
        DB::transaction(function () use ($sellInvoice) {
            $sellInvoice->unlinkSellsFromInvoice();
            $sellInvoice->delete();
        });

        return redirect()
            ->route('admin.sell-invoices.index')
            ->with('success', 'Invoice removed from the list. You can generate a new one for that date and type.');
    }

    public function export(SellInvoice $sellInvoice): StreamedResponse
    {
        $sellInvoice->loadSellsForDisplay();

        $fileName = $sellInvoice->invoice_number . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($sellInvoice) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $title = match ($sellInvoice->invoice_type) {
                SellInvoice::TYPE_CASH => 'Daily Sell Invoice — Cash',
                SellInvoice::TYPE_ONLINE => 'Daily Sell Invoice — Online (UPI / G-Pay)',
                SellInvoice::TYPE_MIX => 'Daily Sell Invoice — Mix (Cash + Online)',
                default => 'Daily Sell Invoice',
            };

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
                    $sheet->setCellValueByColumnAndRow(11, $row, (float) $sell->total_paid);
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
     * Sales eligible for this invoice type (not yet linked on that invoice column).
     */
    protected function eligibleSellsQuery(Carbon $date, string $invoiceType): Builder
    {
        $q = Sell::query()->whereDate('sell_date', $date);

        return match ($invoiceType) {
            SellInvoice::TYPE_CASH => $q->whereNull('cash_sell_invoice_id')->where('payment_mode', 'cash'),
            SellInvoice::TYPE_ONLINE => $q->whereNull('online_sell_invoice_id')->whereIn('payment_mode', ['upi', 'gpay']),
            SellInvoice::TYPE_MIX => $q->whereNull('mix_sell_invoice_id')->where('payment_mode', 'mix'),
            default => $q->whereRaw('1 = 0'),
        };
    }
}
