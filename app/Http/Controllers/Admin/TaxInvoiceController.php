<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTaxInvoiceRequest;
use App\Models\CompanyProfile;
use App\Models\Product;
use App\Models\TaxInvoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TaxInvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = TaxInvoice::query()
            ->withCount('items')
            ->orderBy('invoice_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('admin.tax-invoices.index', compact('invoices'));
    }

    public function create(): View
    {
        $products = Product::active()->orderByName()->get();
        $company = CompanyProfile::first();
        $buyerNames = TaxInvoice::query()
            ->whereNotNull('buyer_name')
            ->distinct()
            ->orderBy('buyer_name')
            ->pluck('buyer_name');

        return view('admin.tax-invoices.create', compact('products', 'company', 'buyerNames'));
    }

    public function store(StoreTaxInvoiceRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $interstate = (bool) ($data['is_interstate'] ?? false);

        $names = $data['product_name'];
        $productIds = $data['product_id'] ?? [];
        $hsnCodes = $data['hsn_code'] ?? [];
        $units = $data['unit_of_measure'] ?? [];
        $quantities = $data['quantity'];
        $rates = $data['rate'];
        $gstRates = $data['gst_rate'];

        $itemsData = [];
        $lines = [];

        foreach ($names as $i => $name) {
            $qty = (float) $quantities[$i];
            $rate = (float) $rates[$i];
            $gst = (float) $gstRates[$i];

            $line = TaxInvoice::computeLine($qty, $rate, $gst, $interstate);
            $lines[] = $line;

            $itemsData[] = array_merge([
                'product_id' => $productIds[$i] ?? null,
                'product_name' => $name,
                'hsn_code' => $hsnCodes[$i] ?? null,
                'unit_of_measure' => $units[$i] ?? null,
                'quantity' => $qty,
                'rate' => $rate,
                'gst_rate' => $gst,
            ], $line);
        }

        $totals = TaxInvoice::aggregateTotals($lines);

        $invoice = DB::transaction(function () use ($data, $interstate, $totals, $itemsData) {
            $invoice = TaxInvoice::create([
                'invoice_number' => TaxInvoice::makeInvoiceNumber(
                    \Carbon\Carbon::parse($data['invoice_date'])
                ),
                'invoice_date' => $data['invoice_date'],
                'buyer_name' => $data['buyer_name'],
                'buyer_address' => $data['buyer_address'] ?? null,
                'buyer_gstin' => $data['buyer_gstin'] ?? null,
                'buyer_state' => $data['buyer_state'] ?? null,
                'buyer_contact_number' => $data['buyer_contact_number'] ?? null,
                'vehicle_no' => $data['vehicle_no'] ?? null,
                'transport' => $data['transport'] ?? null,
                'broker' => $data['broker'] ?? null,
                'eway_bill_no' => $data['eway_bill_no'] ?? null,
                'is_interstate' => $interstate,
                'notes' => $data['notes'] ?? null,
                'taxable_amount' => $totals['taxable_amount'],
                'cgst_amount' => $totals['cgst_amount'],
                'sgst_amount' => $totals['sgst_amount'],
                'igst_amount' => $totals['igst_amount'],
                'grand_total' => $totals['grand_total'],
                'amount_in_words' => $totals['amount_in_words'],
            ]);

            $invoice->items()->createMany($itemsData);

            return $invoice;
        });

        return redirect()
            ->route('admin.tax-invoices.show', $invoice)
            ->with('success', 'Tax invoice ' . $invoice->invoice_number . ' created successfully.');
    }

    public function show(TaxInvoice $taxInvoice): View
    {
        $taxInvoice->load('items');
        $company = CompanyProfile::first();

        return view('admin.tax-invoices.show', compact('taxInvoice', 'company'));
    }

    public function destroy(TaxInvoice $taxInvoice): RedirectResponse
    {
        $number = $taxInvoice->invoice_number;
        $taxInvoice->delete();

        return redirect()
            ->route('admin.tax-invoices.index')
            ->with('success', 'Tax invoice ' . $number . ' removed.');
    }

    public function pdf(TaxInvoice $taxInvoice): Response
    {
        $taxInvoice->load('items');
        $company = CompanyProfile::first();

        $pdf = Pdf::loadView('pdf.tax-invoice', [
            'taxInvoice' => $taxInvoice,
            'company' => $company,
            'logoSrc' => $this->companyLogoDataUri($company),
        ])->setPaper('a4', 'portrait');

        $fileName = str_replace(['/', '\\', ' '], '-', $taxInvoice->invoice_number) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Company logo as a base64 data URI for embedding in the PDF (DomPDF can't
     * reliably resolve storage URLs). Returns null when there is no usable logo.
     */
    protected function companyLogoDataUri(?CompanyProfile $company): ?string
    {
        if (! $company || ! $company->logo) {
            return null;
        }

        try {
            if (! Storage::disk('public')->exists($company->logo)) {
                return null;
            }

            $bytes = Storage::disk('public')->get($company->logo);
            $mime = Storage::disk('public')->mimeType($company->logo) ?: 'image/png';

            return 'data:' . $mime . ';base64,' . base64_encode($bytes);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
