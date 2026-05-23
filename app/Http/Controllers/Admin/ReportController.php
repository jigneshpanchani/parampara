<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Purchase;
use App\Models\Product;
use App\Models\Expense;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display reports dashboard
     */
    public function index()
    {
        $totalSales = Sale::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $totalProfit = $totalSales - $totalPurchases;
        $pendingPayments = Sale::where('payment_status', '!=', 'paid')->sum('pending_amount');
        $totalProducts = Product::count();
        $activeProducts = Product::where('is_active', true)->count();
        $inactiveProducts = $totalProducts - $activeProducts;

        return view('admin.reports.index', compact(
            'totalSales',
            'totalPurchases',
            'totalProfit',
            'pendingPayments',
            'totalProducts',
            'activeProducts',
            'inactiveProducts'
        ));
    }

    /**
     * Display sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $paymentMode = $request->input('payment_mode');

        $query = Sale::with('items.product', 'returns');

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }

        // Apply payment mode filter if provided
        if ($paymentMode) {
            $query->where('payment_mode', $paymentMode);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();

        // Follow-up payments received within the report period (regardless of original sale date).
        $followUpPaymentsQuery = SalePayment::query();
        if ($startDate && $endDate) {
            $followUpPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        if ($paymentMode) {
            $followUpPaymentsQuery->whereHas('sale', fn ($q) => $q->where('payment_mode', $paymentMode));
        }
        $followUpPayments = $followUpPaymentsQuery->get();

        // Get all products for grouping
        $products = Product::all();

        // Calculate totals
        $totalSales = $sales->sum('total_amount');
        $totalQuantity = $sales->flatMap->items->sum('quantity');
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate ?? now()->startOfMonth(), $endDate ?? now()])->sum('amount');

        // Cash / online ACTUALLY received in this period:
        //   at-sale paid portion (by mode)  +  follow-up sale_payments (by method).
        // Pending (pay-later) portions are NOT counted as received.
        $onlineModes = config('payment.sale_modes_online');
        $cashSales   = 0.0;
        $onlineSales = 0.0;
        foreach ($sales as $sale) {
            if ($sale->payment_mode === 'cash') {
                $cashSales += (float) $sale->amount_paid;
            } elseif (in_array($sale->payment_mode, $onlineModes, true)) {
                $onlineSales += (float) $sale->amount_paid;
            } elseif ($sale->payment_mode === 'mix') {
                $cashSales   += (float) ($sale->cash_amount ?? 0);
                $onlineSales += (float) ($sale->online_amount ?? 0);
            }
        }
        foreach ($followUpPayments as $payment) {
            if ($payment->payment_method === 'cash') {
                $cashSales += (float) $payment->amount;
            } else {
                $onlineSales += (float) $payment->amount;
            }
        }

        // Mix card: revenue actually collected via mix-mode sales (cash + online portions of those sales).
        $mixSales = (float) $sales->where('payment_mode', 'mix')
            ->sum(fn ($s) => (float) ($s->cash_amount ?? 0) + (float) ($s->online_amount ?? 0));

        // Paid in this period = cash + online actually received in the period.
        $paidAmount    = $cashSales + $onlineSales;
        $pendingAmount = $sales->sum('pending_amount');

        // Calculate product-wise quantity breakdown
        $quantityByProduct = [];
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $productCode = $item->product?->product_code ?? 'Unknown';
                if (!isset($quantityByProduct[$productCode])) {
                    $quantityByProduct[$productCode] = 0;
                }
                $quantityByProduct[$productCode] += $item->quantity;
            }
        }
        // Sort by product code for consistent display
        ksort($quantityByProduct);

        return view('admin.reports.sales', compact(
            'sales',
            'products',
            'totalSales',
            'totalQuantity',
            'paidAmount',
            'pendingAmount',
            'totalExpenses',
            'cashSales',
            'onlineSales',
            'mixSales',
            'quantityByProduct',
            'startDate',
            'endDate',
            'paymentMode'
        ));
    }

    /**
     * Display purchases report
     */
    public function purchases(Request $request)
    {
        $purchases = $this->getPurchasesForReport($request);
        $totalPurchases = $purchases->sum('total_amount');
        $totalItems = $purchases->flatMap->items->count();

        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $paidStatus = $request->input('paid_status');

        return view('admin.reports.purchases', compact(
            'purchases',
            'totalPurchases',
            'totalItems',
            'dateFrom',
            'dateTo',
            'paidStatus'
        ));
    }

    /**
     * Apply date + paid-status filters (same logic as report + export).
     */
    protected function getPurchasesForReport(Request $request)
    {
        $query = Purchase::with(['items.product', 'payments'])->orderBy('purchase_date', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('purchase_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('purchase_date', '<=', $request->date_to);
        }

        $purchases = $query->get();

        if ($request->filled('paid_status') && $request->paid_status !== '') {
            $purchases = $purchases->filter(function ($purchase) use ($request) {
                return $purchase->getPaymentStatus() === $request->paid_status;
            })->values();
        }

        return $purchases;
    }

    /**
     * Export purchases report to Excel (XLSX).
     */
    public function exportPurchases(Request $request)
    {
        $purchases = $this->getPurchasesForReport($request);
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');
        $paidStatus = $request->input('paid_status');

        $filterParts = [];
        if ($dateFrom || $dateTo) {
            $fromLabel = $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d M Y') : '—';
            $toLabel = $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d M Y') : '—';
            $filterParts[] = "Date: {$fromLabel} to {$toLabel}";
        }
        if ($paidStatus !== null && $paidStatus !== '') {
            $filterParts[] = 'Paid status: ' . ucfirst($paidStatus);
        }
        $filtersLine = count($filterParts) ? implode(' | ', $filterParts) : 'All records (no filters)';

        $generatedAt = now()->format('d M Y H:i');
        $fileName = 'Purchases_Report_' . now()->format('Y-m-d_His') . '.xlsx';

        return response()->streamDownload(function () use ($purchases, $filtersLine, $generatedAt) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'Purchases Report');
            $sheet->mergeCells('A1:I1');
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

            $sheet->setCellValue('A2', 'Generated: ' . $generatedAt);
            $sheet->mergeCells('A2:I2');
            $sheet->getStyle('A2')->getFont()->setItalic(true);

            $sheet->setCellValue('A3', 'Filters: ' . $filtersLine);
            $sheet->mergeCells('A3:I3');
            $sheet->getStyle('A3')->getFont()->setSize(11);

            $headers = ['Date', 'Supplier', 'Items', 'Amount', 'Transport', 'Total', 'Due Date', 'Status', 'Line items'];
            $row = 5;
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, $row, $header);
                $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                $col++;
            }

            $row = 6;
            if ($purchases->isEmpty()) {
                $sheet->setCellValue('A6', 'No purchases match the selected filters.');
                $sheet->mergeCells('A6:I6');
            } else {
                foreach ($purchases as $purchase) {
                    $itemsCount = $purchase->items->count();
                    $itemsAmount = $purchase->items->sum('total_price');
                    $payStatus = ucfirst($purchase->getPaymentStatus());
                    $lineItems = $purchase->items->map(function ($item) {
                        $name = $item->product?->product_name ?? 'Deleted Product';
                        return $name . ' - ' . $item->quantity . ' × ₹' . number_format($item->purchase_price, 2) . ' = ₹' . number_format($item->total_price, 2);
                    })->implode('; ');

                    $sheet->setCellValueByColumnAndRow(1, $row, $purchase->purchase_date->format('d M Y'));
                    $sheet->setCellValueByColumnAndRow(2, $row, $purchase->supplier_name);
                    $sheet->setCellValueByColumnAndRow(3, $row, $itemsCount);
                    $sheet->setCellValueByColumnAndRow(4, $row, (float) $itemsAmount);
                    $sheet->setCellValueByColumnAndRow(5, $row, (float) ($purchase->transportation_cost ?? 0));
                    $sheet->setCellValueByColumnAndRow(6, $row, (float) $purchase->total_amount);
                    $sheet->setCellValueByColumnAndRow(7, $row, $purchase->bill_due_date ? $purchase->bill_due_date->format('d M Y') : '-');
                    $sheet->setCellValueByColumnAndRow(8, $row, $payStatus);
                    $sheet->setCellValueByColumnAndRow(9, $row, $lineItems);
                    $row++;
                }
            }

            for ($i = 1; $i <= 9; $i++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export sales report to Excel
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sale::with('items.product', 'returns');

        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }

        $sales = $query->orderBy('sale_date', 'desc')->get();
        $products = Product::orderBy('id')->get();

        // Follow-up payments received within the period (regardless of when the original sale happened).
        // Eager-load the parent sale so the Notes column can quote the bill date / seller.
        $followUpPaymentsQuery = SalePayment::with('sale');
        if ($startDate && $endDate) {
            $followUpPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        $followUpPayments = $followUpPaymentsQuery->get();

        // Create Excel file
        $fileName = 'Sales_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($sales, $products, $followUpPayments, $startDate, $endDate) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header block
            $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
            $periodLabel = $startDate && $endDate
                ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
                : 'All records';
            $monthYear   = $startDate ? date('M-y', strtotime($startDate)) : 'All';

            $sheet->setCellValue('A1', $companyName . ' — Sales Report');
            $sheet->setCellValue('A2', 'Period: ' . $periodLabel . '   |   Generated: ' . now()->format('d M Y H:i'));

            // Build dynamic headers - Date + Product Codes + Sales/Return/Expense columns
            $headers = ['Date'];
            foreach ($products as $product) {
                $headers[] = $product->product_code;
            }
            $headers = array_merge($headers, ['Online', 'Cash', 'Return', 'Return Details', 'Total', 'Notes', 'Expense', 'Exp.Detail', 'Total']);

            // Write headers
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 3, $header);
                $sheet->getStyleByColumnAndRow($col, 3)->getFont()->setBold(true);
                $col++;
            }

            // Merge the two title rows across the full report width, then center them
            // (apply styles to the merged range so the alignment sticks).
            $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->mergeCells("A2:{$lastColLetter}2");

            $title1Style = $sheet->getStyle("A1:{$lastColLetter}1");
            $title1Style->getFont()->setBold(true)->setSize(14);
            $title1Style->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $title2Style = $sheet->getStyle("A2:{$lastColLetter}2");
            $title2Style->getFont()->setItalic(true)->setSize(10);
            $title2Style->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

            $sheet->getRowDimension(1)->setRowHeight(22);
            $sheet->getRowDimension(2)->setRowHeight(18);

            // Calculate column positions for sales/expense data
            $productCount = $products->count();
            $onlineCol = 2 + $productCount;
            $cashCol = $onlineCol + 1;
            $returnCol = $cashCol + 1;
            $returnDetailsCol = $returnCol + 1;
            $totalCol = $returnDetailsCol + 1;
            $notesCol = $totalCol + 1;
            $expenseCol = $notesCol + 1;
            $expenseDetailsCol = $expenseCol + 1;
            $finalTotalCol = $expenseDetailsCol + 1;

            // Rotate header text 90° for product code columns + Online / Cash / Return / Return Details.
            // Date / Total / Notes / Expense / Exp.Detail / Final Total stay horizontal.
            for ($c = 2; $c <= $returnDetailsCol; $c++) {
                $alignment = $sheet->getStyleByColumnAndRow($c, 3)->getAlignment();
                $alignment->setTextRotation(90);
                $alignment->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM);
                $alignment->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getRowDimension(3)->setRowHeight(85);

            // Group sales by date
            $salesByDate = $sales->groupBy(function ($sale) {
                return $sale->sale_date->format('d-m-Y');
            });

            // Group follow-up payments by their actual receipt date.
            $paymentsByDate = $followUpPayments->groupBy(function ($payment) {
                return $payment->payment_date->format('d-m-Y');
            });

            // Iterate over the union of dates so days that only had follow-up payments still appear.
            // Ascending: 1 Apr, 2 Apr, 3 Apr ...
            $allDates = $salesByDate->keys()
                ->concat($paymentsByDate->keys())
                ->unique()
                ->sortBy(function ($d) {
                    return \DateTime::createFromFormat('d-m-Y', $d)->format('Y-m-d');
                })
                ->values();

            $row = 4;
            $totalCash = 0;
            $totalOnline = 0;
            $totalExpense = 0;

            // Initialize product-wise totals
            $productTotals = [];
            foreach ($products as $product) {
                $productTotals[$product->product_code] = 0;
            }

            $onlineModes = config('payment.sale_modes_online');

            foreach ($allDates as $date) {
                $dateSales    = $salesByDate->get($date, collect());
                $datePayments = $paymentsByDate->get($date, collect());

                // Get product quantities for this date
                $productQtys = [];
                foreach ($products as $product) {
                    $productQtys[$product->product_code] = 0;
                }

                $dateCashAmount = 0;
                $dateOnlineAmount = 0;
                $dateReturnAmount = 0;
                $dateExpenseAmount = 0;

                // At-sale contribution: only the portion ACTUALLY paid at sale time, by mode.
                // Pending pay-later amounts must NOT be counted as received cash/online.
                foreach ($dateSales as $sale) {
                    if ($sale->payment_mode === 'cash') {
                        $dateCashAmount += (float) $sale->amount_paid;
                    } elseif (in_array($sale->payment_mode, $onlineModes, true)) {
                        $dateOnlineAmount += (float) $sale->amount_paid;
                    } elseif ($sale->payment_mode === 'mix') {
                        $dateCashAmount   += (float) ($sale->cash_amount ?? 0);
                        $dateOnlineAmount += (float) ($sale->online_amount ?? 0);
                    }

                    foreach ($sale->items as $item) {
                        $code = $item->product?->product_code ?? '';
                        if ($code && isset($productQtys[$code])) {
                            $productQtys[$code] += $item->quantity;
                        }
                    }
                }

                // Follow-up payments actually received on this date (for any sale, possibly older).
                foreach ($datePayments as $payment) {
                    if ($payment->payment_method === 'cash') {
                        $dateCashAmount += (float) $payment->amount;
                    } else {
                        $dateOnlineAmount += (float) $payment->amount;
                    }
                }

                // Get returns for this date
                foreach ($dateSales as $sale) {
                    foreach ($sale->returns as $return) {
                        $dateReturnAmount += $return->total_return_amount;
                    }
                }

                // Get expenses for this date
                // Convert date from d-m-Y to Y-m-d format for database query
                $dbDate = \DateTime::createFromFormat('d-m-Y', $date)->format('Y-m-d');
                $dateExpenses = Expense::with('expenseCategory')->whereDate('expense_date', $dbDate)->get();
                foreach ($dateExpenses as $expense) {
                    $dateExpenseAmount += $expense->amount;
                }

                // Write date
                $sheet->setCellValueByColumnAndRow(1, $row, $date);

                // Write product quantities dynamically
                $col = 2;
                foreach ($products as $product) {
                    $code = $product->product_code;
                    if (isset($productQtys[$code]) && $productQtys[$code] > 0) {
                        $sheet->setCellValueByColumnAndRow($col, $row, $productQtys[$code]);
                        // Accumulate product totals
                        $productTotals[$code] += $productQtys[$code];
                    }
                    $col++;
                }

                // Write online amount (dynamic column)
                $sheet->setCellValueByColumnAndRow($onlineCol, $row, $dateOnlineAmount > 0 ? $dateOnlineAmount : '');

                // Write cash amount (dynamic column)
                $sheet->setCellValueByColumnAndRow($cashCol, $row, $dateCashAmount > 0 ? $dateCashAmount : '');

                // Write return info (dynamic column)
                $sheet->setCellValueByColumnAndRow($returnCol, $row, $dateReturnAmount > 0 ? $dateReturnAmount : '');

                // Write return details (dynamic column)
                $returnDetails = '';
                foreach ($dateSales as $sale) {
                    foreach ($sale->returns as $return) {
                        $returnDetails .= ($return->product?->product_code ?? 'N/A') . ': ' . $return->quantity . '; ';
                    }
                }
                $sheet->setCellValueByColumnAndRow($returnDetailsCol, $row, trim($returnDetails));

                // Write total (dynamic column) - Sales - Returns
                $total = ($dateCashAmount + $dateOnlineAmount) - $dateReturnAmount;
                $sheet->setCellValueByColumnAndRow($totalCol, $row, $total);

                // Build Notes for follow-up payments received this date
                // (i.e., money received for bills dated other days). Sale-day rows leave Notes empty.
                $noteParts = [];
                foreach ($datePayments as $payment) {
                    $sale = $payment->sale;
                    $billDate = $sale && $sale->sale_date ? $sale->sale_date->format('d M Y') : '—';
                    $seller   = $sale && $sale->seller_name ? $sale->seller_name : 'Unknown';
                    $methodLabel = config("payment.sale_payment_methods.{$payment->payment_method}", strtoupper((string) $payment->payment_method));
                    $line = '₹' . number_format((float) $payment->amount, 0) . ' ' . $methodLabel
                          . ' for bill of ' . $billDate . ' (' . $seller . ')';
                    if (!empty($payment->notes)) {
                        $line .= ' — ' . $payment->notes;
                    }
                    $noteParts[] = $line;
                }
                if (!empty($noteParts)) {
                    $sheet->setCellValueByColumnAndRow($notesCol, $row, implode("\n", $noteParts));
                    $sheet->getStyleByColumnAndRow($notesCol, $row)->getAlignment()->setWrapText(true);
                }

                // Write expense (dynamic column)
                $sheet->setCellValueByColumnAndRow($expenseCol, $row, $dateExpenseAmount > 0 ? $dateExpenseAmount : '');

                // Write expense details (dynamic column) — one entry per line inside the cell.
                $expenseDetailsParts = [];
                foreach ($dateExpenses as $expense) {
                    $categoryName = $expense->expenseCategory ? $expense->expenseCategory->name : 'Other';
                    $label = $expense->notes ? $categoryName . ' - ' . $expense->notes : $categoryName;
                    $expenseDetailsParts[] = number_format($expense->amount, 0) . '/- ' . $label;
                }
                $sheet->setCellValueByColumnAndRow($expenseDetailsCol, $row, implode("\n", $expenseDetailsParts));

                // Write final total (dynamic column) - Sales - Returns - Expenses
                $finalTotal = $total - $dateExpenseAmount;
                $sheet->setCellValueByColumnAndRow($finalTotalCol, $row, $finalTotal);

                $totalCash += $dateCashAmount;
                $totalOnline += $dateOnlineAmount;
                $totalExpense += $dateExpenseAmount;
                $row++;
            }

            // Add total row
            $row++; // Skip one row
            $sheet->setCellValueByColumnAndRow(1, $row, 'TOTAL');
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);

            // Write product-wise totals
            $col = 2;
            foreach ($products as $product) {
                $code = $product->product_code;
                if (isset($productTotals[$code]) && $productTotals[$code] > 0) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $productTotals[$code]);
                    $sheet->getStyleByColumnAndRow($col, $row)->getFont()->setBold(true);
                }
                $col++;
            }

            // Total Online (dynamic column)
            $sheet->setCellValueByColumnAndRow($onlineCol, $row, $totalOnline);
            $sheet->getStyleByColumnAndRow($onlineCol, $row)->getFont()->setBold(true);

            // Total Cash (dynamic column)
            $sheet->setCellValueByColumnAndRow($cashCol, $row, $totalCash);
            $sheet->getStyleByColumnAndRow($cashCol, $row)->getFont()->setBold(true);

            // Total Expense (dynamic column)
            $sheet->setCellValueByColumnAndRow($expenseCol, $row, $totalExpense);
            $sheet->getStyleByColumnAndRow($expenseCol, $row)->getFont()->setBold(true);

            // Explicit column widths so the sheet doesn't end up either way too wide
            // (autoSize on text columns) or way too narrow (autoSize on rotated headers).
            //   1            : Date           — narrow text
            //   2..onlineCol-1 : Product cols  — narrow numeric (1-3 digits)
            //   onlineCol..totalCol            : Money / return / total — medium
            //   notesCol                       : Notes — wide, wrapped
            //   expenseCol..finalTotalCol      : Expense / Exp.Detail / Net
            for ($i = 1; $i <= $finalTotalCol; $i++) {
                $width = match (true) {
                    $i === 1                                  => 11,        // Date
                    $i >= 2 && $i < $onlineCol                => 5,         // Product cols (vertical headers)
                    $i === $onlineCol || $i === $cashCol      => 9,         // Online / Cash
                    $i === $returnCol                         => 8,         // Return
                    $i === $returnDetailsCol                  => 18,        // Return Details (text)
                    $i === $totalCol                          => 9,         // Total (sales-net-of-returns)
                    $i === $notesCol                          => 38,        // Notes (long text, wrapped)
                    $i === $expenseCol                        => 9,         // Expense
                    $i === $expenseDetailsCol                 => 28,        // Exp.Detail
                    $i === $finalTotalCol                     => 10,        // Final Total / Net
                    default                                   => 8,
                };
                $sheet->getColumnDimensionByColumn($i)->setWidth($width);
            }
            // Wrap text on the long-form columns so multi-line entries stay inside the cell.
            foreach ([$returnDetailsCol, $notesCol, $expenseDetailsCol] as $wrapCol) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($wrapCol);
                $sheet->getStyle("{$colLetter}4:{$colLetter}{$row}")
                    ->getAlignment()
                    ->setWrapText(true)
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP);
            }

            // Add product-wise payment mode breakdown sections
            $row += 3; // Skip rows for spacing

            // Calculate product-wise breakdown by payment mode
            $productPaymentQty = []; // [product_code][payment_mode] = quantity
            $productPaymentAmount = []; // [product_code][payment_mode] = amount

            foreach ($products as $product) {
                $productPaymentQty[$product->product_code] = [
                    'upi' => 0,
                    'gpay' => 0,
                    'cash' => 0,
                    'mix' => 0
                ];
                $productPaymentAmount[$product->product_code] = [
                    'upi' => 0,
                    'gpay' => 0,
                    'cash' => 0,
                    'mix' => 0
                ];
            }

            // Aggregate data by product and payment mode
            foreach ($sales as $sale) {
                foreach ($sale->items as $item) {
                    $code = $item->product?->product_code ?? '';
                    $paymentMode = $sale->payment_mode;

                    if ($code && isset($productPaymentQty[$code][$paymentMode])) {
                        $productPaymentQty[$code][$paymentMode] += $item->quantity;
                        $productPaymentAmount[$code][$paymentMode] += $item->total_price;
                    }
                }
            }

            // Section 1: Product-wise Quantity by Payment Mode
            $sheet->setCellValueByColumnAndRow(1, $row, 'Product-wise Quantity by Payment Mode');
            $sheet->mergeCells("A{$row}:F{$row}"); // Merge A to F for title
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true)->setSize(12);
            $row++;

            // Headers for quantity section
            $sheet->setCellValueByColumnAndRow(1, $row, 'Product');
            $sheet->setCellValueByColumnAndRow(2, $row, 'UPI');
            $sheet->setCellValueByColumnAndRow(3, $row, 'G-PAY');
            $sheet->setCellValueByColumnAndRow(4, $row, 'CASH');
            $sheet->setCellValueByColumnAndRow(5, $row, 'MIX');
            $sheet->setCellValueByColumnAndRow(6, $row, 'Total');
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(2, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(3, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(4, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(5, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(6, $row)->getFont()->setBold(true);
            $row++;

            // Write quantity data (only for products with sales)
            foreach ($products as $product) {
                $code = $product->product_code;
                $totalQty = $productPaymentQty[$code]['upi'] + $productPaymentQty[$code]['gpay'] +
                           $productPaymentQty[$code]['cash'] + $productPaymentQty[$code]['mix'];

                // Skip products with no sales
                if ($totalQty == 0) {
                    continue;
                }

                $sheet->setCellValueByColumnAndRow(1, $row, $code);
                $sheet->setCellValueByColumnAndRow(2, $row, $productPaymentQty[$code]['upi'] > 0 ? $productPaymentQty[$code]['upi'] : '');
                $sheet->setCellValueByColumnAndRow(3, $row, $productPaymentQty[$code]['gpay'] > 0 ? $productPaymentQty[$code]['gpay'] : '');
                $sheet->setCellValueByColumnAndRow(4, $row, $productPaymentQty[$code]['cash'] > 0 ? $productPaymentQty[$code]['cash'] : '');
                $sheet->setCellValueByColumnAndRow(5, $row, $productPaymentQty[$code]['mix'] > 0 ? $productPaymentQty[$code]['mix'] : '');
                $sheet->setCellValueByColumnAndRow(6, $row, $totalQty);
                $row++;
            }

            // Section 2: Product-wise Amount by Payment Mode
            $row += 2; // Skip rows for spacing
            $sheet->setCellValueByColumnAndRow(1, $row, 'Product-wise Amount by Payment Mode');
            $sheet->mergeCells("A{$row}:F{$row}"); // Merge A to F for title
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true)->setSize(12);
            $row++;

            // Headers for amount section
            $sheet->setCellValueByColumnAndRow(1, $row, 'Product');
            $sheet->setCellValueByColumnAndRow(2, $row, 'UPI');
            $sheet->setCellValueByColumnAndRow(3, $row, 'G-PAY');
            $sheet->setCellValueByColumnAndRow(4, $row, 'CASH');
            $sheet->setCellValueByColumnAndRow(5, $row, 'MIX');
            $sheet->setCellValueByColumnAndRow(6, $row, 'Total');
            $sheet->getStyleByColumnAndRow(1, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(2, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(3, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(4, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(5, $row)->getFont()->setBold(true);
            $sheet->getStyleByColumnAndRow(6, $row)->getFont()->setBold(true);
            $row++;

            // Write amount data (only for products with sales)
            foreach ($products as $product) {
                $code = $product->product_code;
                $totalAmount = $productPaymentAmount[$code]['upi'] + $productPaymentAmount[$code]['gpay'] +
                              $productPaymentAmount[$code]['cash'] + $productPaymentAmount[$code]['mix'];

                // Skip products with no sales
                if ($totalAmount == 0) {
                    continue;
                }

                $sheet->setCellValueByColumnAndRow(1, $row, $code);
                $sheet->setCellValueByColumnAndRow(2, $row, $productPaymentAmount[$code]['upi'] > 0 ? $productPaymentAmount[$code]['upi'] : '');
                $sheet->setCellValueByColumnAndRow(3, $row, $productPaymentAmount[$code]['gpay'] > 0 ? $productPaymentAmount[$code]['gpay'] : '');
                $sheet->setCellValueByColumnAndRow(4, $row, $productPaymentAmount[$code]['cash'] > 0 ? $productPaymentAmount[$code]['cash'] : '');
                $sheet->setCellValueByColumnAndRow(5, $row, $productPaymentAmount[$code]['mix'] > 0 ? $productPaymentAmount[$code]['mix'] : '');
                $sheet->setCellValueByColumnAndRow(6, $row, $totalAmount);
                $row++;
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    /**
     * Export sales report to PDF (2-page layout: daily breakdown + product-wise breakdown).
     */
    public function exportSalesPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $paymentMode = $request->input('payment_mode');

        $query = Sale::with('items.product', 'returns');
        if ($startDate && $endDate) {
            $query->whereBetween('sale_date', [$startDate, $endDate]);
        }
        if ($paymentMode) {
            $query->where('payment_mode', $paymentMode);
        }
        $sales = $query->orderBy('sale_date', 'desc')->get();

        $followUpPaymentsQuery = SalePayment::with('sale');
        if ($startDate && $endDate) {
            $followUpPaymentsQuery->whereBetween('payment_date', [$startDate, $endDate]);
        }
        if ($paymentMode) {
            $followUpPaymentsQuery->whereHas('sale', fn ($q) => $q->where('payment_mode', $paymentMode));
        }
        $followUpPayments = $followUpPaymentsQuery->get();

        $products = Product::orderBy('id')->get();

        $aggregated = $this->aggregateSalesForReport($sales, $followUpPayments, $products, $startDate, $endDate);

        $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
        $periodLabel = $startDate && $endDate
            ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
            : 'All records';
        $monthLabel = $startDate ? \Carbon\Carbon::parse($startDate)->format('M Y') : 'All';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.sales-report', [
            'products'            => $products,
            'dailyRows'           => $aggregated['dailyRows'],
            'totals'              => $aggregated['totals'],
            'productPaymentQty'   => $aggregated['productPaymentQty'],
            'productPaymentAmount' => $aggregated['productPaymentAmount'],
            'companyName'         => $companyName,
            'periodLabel'         => $periodLabel,
            'monthLabel'          => $monthLabel,
            'generatedAt'         => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        $fileName = 'Sales_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Aggregate sales + follow-up payments for the daily report and product-wise breakdown.
     * Mirrors the math used by exportSales so the PDF and Excel agree.
     */
    protected function aggregateSalesForReport($sales, $followUpPayments, $products, $startDate, $endDate): array
    {
        $onlineModes = config('payment.sale_modes_online');

        $salesByDate = $sales->groupBy(fn ($s) => $s->sale_date->format('Y-m-d'));
        $paymentsByDate = $followUpPayments->groupBy(fn ($p) => $p->payment_date->format('Y-m-d'));

        // Ascending: 1 Apr, 2 Apr, 3 Apr ...
        $allDates = $salesByDate->keys()
            ->concat($paymentsByDate->keys())
            ->unique()
            ->sort()
            ->values();

        $productCodes  = $products->pluck('product_code')->all();
        $productTotals = array_fill_keys($productCodes, 0);

        $totalCash = 0; $totalOnline = 0; $totalExpense = 0; $totalReturns = 0;
        $dailyRows = [];

        foreach ($allDates as $isoDate) {
            $dateSales    = $salesByDate->get($isoDate, collect());
            $datePayments = $paymentsByDate->get($isoDate, collect());

            $productQtys = array_fill_keys($productCodes, 0);
            $cashAmount  = 0; $onlineAmount = 0; $returnAmount = 0; $expenseAmount = 0;

            foreach ($dateSales as $sale) {
                if ($sale->payment_mode === 'cash') {
                    $cashAmount += (float) $sale->amount_paid;
                } elseif (in_array($sale->payment_mode, $onlineModes, true)) {
                    $onlineAmount += (float) $sale->amount_paid;
                } elseif ($sale->payment_mode === 'mix') {
                    $cashAmount   += (float) ($sale->cash_amount ?? 0);
                    $onlineAmount += (float) ($sale->online_amount ?? 0);
                }

                foreach ($sale->items as $item) {
                    $code = $item->product?->product_code;
                    if ($code && array_key_exists($code, $productQtys)) {
                        $productQtys[$code] += $item->quantity;
                    }
                }

                foreach ($sale->returns as $return) {
                    $returnAmount += (float) $return->total_return_amount;
                }
            }

            foreach ($datePayments as $payment) {
                if ($payment->payment_method === 'cash') {
                    $cashAmount += (float) $payment->amount;
                } else {
                    $onlineAmount += (float) $payment->amount;
                }
            }

            $dateExpenses = Expense::with('expenseCategory')->whereDate('expense_date', $isoDate)->get();
            foreach ($dateExpenses as $expense) {
                $expenseAmount += (float) $expense->amount;
            }

            $returnDetails = [];
            foreach ($dateSales as $sale) {
                foreach ($sale->returns as $return) {
                    $returnDetails[] = ($return->product?->product_code ?? 'N/A') . ': ' . $return->quantity;
                }
            }

            $expenseDetails = [];
            foreach ($dateExpenses as $expense) {
                $catName = $expense->expenseCategory ? $expense->expenseCategory->name : 'Other';
                $label = $expense->notes ? $catName . ' - ' . $expense->notes : $catName;
                $expenseDetails[] = number_format((float) $expense->amount, 0) . '/- ' . $label;
            }

            $notes = [];
            foreach ($datePayments as $payment) {
                $sale = $payment->sale;
                $billDate = $sale && $sale->sale_date ? $sale->sale_date->format('d M Y') : '—';
                $seller   = $sale && $sale->seller_name ? $sale->seller_name : 'Unknown';
                $methodLabel = config("payment.sale_payment_methods.{$payment->payment_method}", strtoupper((string) $payment->payment_method));
                $line = '₹' . number_format((float) $payment->amount, 0) . ' ' . $methodLabel
                      . ' for bill of ' . $billDate . ' (' . $seller . ')';
                if (!empty($payment->notes)) {
                    $line .= ' — ' . $payment->notes;
                }
                $notes[] = $line;
            }

            foreach ($productCodes as $code) {
                $productTotals[$code] += $productQtys[$code];
            }
            $totalCash   += $cashAmount;
            $totalOnline += $onlineAmount;
            $totalExpense += $expenseAmount;
            $totalReturns += $returnAmount;

            $rowTotal = ($cashAmount + $onlineAmount) - $returnAmount;

            $dailyRows[] = [
                'date'            => \Carbon\Carbon::parse($isoDate)->format('d-m-Y'),
                'product_qtys'    => $productQtys,
                'online'          => $onlineAmount,
                'cash'            => $cashAmount,
                'return'          => $returnAmount,
                'return_details'  => implode('; ', $returnDetails),
                'total'           => $rowTotal,
                'notes'           => implode("\n", $notes),
                'expense'         => $expenseAmount,
                'expense_details' => implode("\n", $expenseDetails),
                'final_total'     => $rowTotal - $expenseAmount,
            ];
        }

        // Product-wise quantity / amount by payment mode (uses ALL sales in range, not only days with sales).
        $productPaymentQty = [];
        $productPaymentAmount = [];
        foreach ($products as $product) {
            $productPaymentQty[$product->product_code]    = ['upi' => 0, 'gpay' => 0, 'cash' => 0, 'mix' => 0];
            $productPaymentAmount[$product->product_code] = ['upi' => 0, 'gpay' => 0, 'cash' => 0, 'mix' => 0];
        }
        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $code = $item->product?->product_code;
                $mode = $sale->payment_mode;
                if ($code && isset($productPaymentQty[$code][$mode])) {
                    $productPaymentQty[$code][$mode]    += $item->quantity;
                    $productPaymentAmount[$code][$mode] += $item->total_price;
                }
            }
        }

        return [
            'dailyRows'            => $dailyRows,
            'totals'               => [
                'cash'        => $totalCash,
                'online'      => $totalOnline,
                'returns'     => $totalReturns,
                'expense'     => $totalExpense,
                'product'     => $productTotals,
                'grand_total' => ($totalCash + $totalOnline) - $totalReturns - $totalExpense,
            ],
            'productPaymentQty'    => $productPaymentQty,
            'productPaymentAmount' => $productPaymentAmount,
        ];
    }

    /**
     * Display Counter report: date-wise Cash / Online / Pay Later / Return / Expense / Net.
     */
    public function counter(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $productIds = $this->normalizeProductIds($request->input('product_ids'));

        $aggregated = $this->aggregateCounterReport($startDate, $endDate, $productIds);

        $allProducts = Product::orderByName()->get(['id', 'product_name', 'product_code']);

        return view('admin.reports.counter', [
            'rows'              => $aggregated['rows'],
            'totals'            => $aggregated['totals'],
            'startDate'         => $startDate,
            'endDate'           => $endDate,
            'allProducts'       => $allProducts,
            'selectedProductIds' => $productIds,
        ]);
    }

    /**
     * Normalize product_ids request value into a clean int[] or null (= all).
     */
    protected function normalizeProductIds($raw): ?array
    {
        if ($raw === null || $raw === '' || $raw === []) {
            return null;
        }
        $ids = is_array($raw) ? $raw : explode(',', (string) $raw);
        $ids = array_values(array_filter(array_map('intval', $ids), fn ($i) => $i > 0));
        return empty($ids) ? null : $ids;
    }

    /**
     * Build the daily counter rows + summary totals.
     *
     * Cash / Online = money actually received that day:
     *   at-sale paid portion (by mode) + follow-up sale_payments (by method).
     * Pay Later     = unpaid portion of sales recorded that day, as a snapshot at sale time
     *                 (total_amount - amount_paid). Stays accurate after the customer pays
     *                 later — `amount_paid` is set at sale time and is never modified by
     *                 follow-up payments, only `pending_amount` is recalculated.
     * Return        = sale returns recorded that day (uses return_date).
     * Expense       = expenses recorded that day.
     */
    protected function aggregateCounterReport(?string $startDate, ?string $endDate, ?array $productIds = null): array
    {
        $onlineModes = config('payment.sale_modes_online');

        $salesQuery = Sale::with('items');
        $paymentsQuery = SalePayment::query();
        $returnsQuery = \App\Models\SaleReturn::query();
        $expensesQuery = Expense::query();

        if ($startDate) {
            $salesQuery->whereDate('sale_date', '>=', $startDate);
            $paymentsQuery->whereDate('payment_date', '>=', $startDate);
            $returnsQuery->whereDate('return_date', '>=', $startDate);
            $expensesQuery->whereDate('expense_date', '>=', $startDate);
        }
        if ($endDate) {
            $salesQuery->whereDate('sale_date', '<=', $endDate);
            $paymentsQuery->whereDate('payment_date', '<=', $endDate);
            $returnsQuery->whereDate('return_date', '<=', $endDate);
            $expensesQuery->whereDate('expense_date', '<=', $endDate);
        }

        // Product filter: include any sale/return that involves at least one of the selected products.
        // Expenses are intentionally NOT product-filtered (they are shop overhead, not per-product).
        if (!empty($productIds)) {
            $salesQuery->whereHas('items', fn ($q) => $q->whereIn('product_id', $productIds));
            $paymentsQuery->whereHas('sale.items', fn ($q) => $q->whereIn('product_id', $productIds));
            $returnsQuery->whereIn('product_id', $productIds);
        }

        $sales            = $salesQuery->get();
        $followUpPayments = $paymentsQuery->get();
        $saleReturns      = $returnsQuery->get();
        $expenses         = $expensesQuery->get();

        $salesByDate    = $sales->groupBy(fn ($s) => $s->sale_date->format('Y-m-d'));
        $paymentsByDate = $followUpPayments->groupBy(fn ($p) => $p->payment_date->format('Y-m-d'));
        $returnsByDate  = $saleReturns->groupBy(fn ($r) => $r->return_date->format('Y-m-d'));
        $expensesByDate = $expenses->groupBy(fn ($e) => $e->expense_date->format('Y-m-d'));

        $allDates = $salesByDate->keys()
            ->concat($paymentsByDate->keys())
            ->concat($returnsByDate->keys())
            ->concat($expensesByDate->keys())
            ->unique()
            ->sort()
            ->values();

        $rows = [];
        $totalCash = 0; $totalOnline = 0; $totalPayLater = 0;
        $totalReturn = 0; $totalExpense = 0; $totalSalesAmount = 0;

        foreach ($allDates as $isoDate) {
            $dateSales    = $salesByDate->get($isoDate, collect());
            $datePayments = $paymentsByDate->get($isoDate, collect());
            $dateReturns  = $returnsByDate->get($isoDate, collect());
            $dateExpenses = $expensesByDate->get($isoDate, collect());

            $cash = 0.0; $online = 0.0; $payLater = 0.0; $salesAmount = 0.0;

            foreach ($dateSales as $sale) {
                $salesAmount += (float) $sale->total_amount;
                // Snapshot of pay-later at sale time. Using `pending_amount` here would let
                // follow-up payments retroactively zero out the original Pay Later on this date.
                $payLater    += max(0.0, (float) $sale->total_amount - (float) $sale->amount_paid);

                if ($sale->payment_mode === 'cash') {
                    $cash += (float) $sale->amount_paid;
                } elseif (in_array($sale->payment_mode, $onlineModes, true)) {
                    $online += (float) $sale->amount_paid;
                } elseif ($sale->payment_mode === 'mix') {
                    $cash   += (float) ($sale->cash_amount ?? 0);
                    $online += (float) ($sale->online_amount ?? 0);
                }
            }

            foreach ($datePayments as $payment) {
                if ($payment->payment_method === 'cash') {
                    $cash += (float) $payment->amount;
                } else {
                    $online += (float) $payment->amount;
                }
            }

            $returnAmount  = (float) $dateReturns->sum('total_return_amount');
            $expenseAmount = (float) $dateExpenses->sum('amount');
            $net           = ($cash + $online) - $returnAmount - $expenseAmount;

            $rows[] = [
                'date'         => \Carbon\Carbon::parse($isoDate)->format('d-m-Y'),
                'date_iso'     => $isoDate,
                'cash'         => $cash,
                'online'       => $online,
                'pay_later'    => $payLater,
                'sales_amount' => $salesAmount,
                'return'       => $returnAmount,
                'expense'      => $expenseAmount,
                'net'          => $net,
            ];

            $totalCash        += $cash;
            $totalOnline      += $online;
            $totalPayLater    += $payLater;
            $totalReturn      += $returnAmount;
            $totalExpense     += $expenseAmount;
            $totalSalesAmount += $salesAmount;
        }

        return [
            'rows'   => $rows,
            'totals' => [
                'cash'         => $totalCash,
                'online'       => $totalOnline,
                'pay_later'    => $totalPayLater,
                'sales_amount' => $totalSalesAmount,
                'return'       => $totalReturn,
                'expense'      => $totalExpense,
                'net'          => ($totalCash + $totalOnline) - $totalReturn - $totalExpense,
            ],
        ];
    }

    /**
     * Export Counter report to Excel (XLSX).
     */
    public function exportCounter(Request $request)
    {
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');
        $productIds = $this->normalizeProductIds($request->input('product_ids'));

        $aggregated = $this->aggregateCounterReport($startDate, $endDate, $productIds);
        $rows       = $aggregated['rows'];
        $totals     = $aggregated['totals'];

        $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
        $periodLabel = $startDate && $endDate
            ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
            : 'All records';

        $productLabel = !empty($productIds)
            ? Product::whereIn('id', $productIds)->orderByName()->pluck('product_name')->implode(', ')
            : 'All products';

        $fileName = 'Counter_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($rows, $totals, $companyName, $periodLabel, $productLabel) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['Date', 'Cash', 'Online', 'Pay Later', 'Total Sales', 'Return', 'Expense', 'Net Total'];
            $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

            $sheet->setCellValue('A1', $companyName . ' — Counter Report');
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Period: ' . $periodLabel . '   |   Generated: ' . now()->format('d M Y H:i'));
            $sheet->mergeCells("A2:{$lastColLetter}2");
            $sheet->getStyle('A2')->getFont()->setItalic(true);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A3', 'Products: ' . $productLabel);
            $sheet->mergeCells("A3:{$lastColLetter}3");
            $sheet->getStyle('A3')->getFont()->setSize(10);
            $sheet->getStyle('A3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $headerRow = 5;
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, $headerRow, $header);
                $sheet->getStyleByColumnAndRow($col, $headerRow)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $headerRow)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E5E7EB');
                $col++;
            }

            $row = $headerRow + 1;
            if (empty($rows)) {
                $sheet->setCellValueByColumnAndRow(1, $row, 'No data for the selected period.');
                $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            } else {
                foreach ($rows as $r) {
                    $sheet->setCellValueByColumnAndRow(1, $row, $r['date']);
                    $sheet->setCellValueByColumnAndRow(2, $row, (float) $r['cash']);
                    $sheet->setCellValueByColumnAndRow(3, $row, (float) $r['online']);
                    $sheet->setCellValueByColumnAndRow(4, $row, (float) $r['pay_later']);
                    $sheet->setCellValueByColumnAndRow(5, $row, (float) $r['sales_amount']);
                    $sheet->setCellValueByColumnAndRow(6, $row, (float) $r['return']);
                    $sheet->setCellValueByColumnAndRow(7, $row, (float) $r['expense']);
                    $sheet->setCellValueByColumnAndRow(8, $row, (float) $r['net']);
                    $row++;
                }

                $sheet->setCellValueByColumnAndRow(1, $row, 'TOTAL');
                $sheet->setCellValueByColumnAndRow(2, $row, (float) $totals['cash']);
                $sheet->setCellValueByColumnAndRow(3, $row, (float) $totals['online']);
                $sheet->setCellValueByColumnAndRow(4, $row, (float) $totals['pay_later']);
                $sheet->setCellValueByColumnAndRow(5, $row, (float) $totals['sales_amount']);
                $sheet->setCellValueByColumnAndRow(6, $row, (float) $totals['return']);
                $sheet->setCellValueByColumnAndRow(7, $row, (float) $totals['expense']);
                $sheet->setCellValueByColumnAndRow(8, $row, (float) $totals['net']);
                $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }

            $widths = [12, 12, 12, 12, 14, 12, 12, 14];
            foreach ($widths as $i => $w) {
                $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export Counter report to PDF.
     */
    public function exportCounterPdf(Request $request)
    {
        $startDate  = $request->input('start_date');
        $endDate    = $request->input('end_date');
        $productIds = $this->normalizeProductIds($request->input('product_ids'));

        $aggregated = $this->aggregateCounterReport($startDate, $endDate, $productIds);

        $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
        $periodLabel = $startDate && $endDate
            ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
            : 'All records';
        $productLabel = !empty($productIds)
            ? Product::whereIn('id', $productIds)->orderByName()->pluck('product_name')->implode(', ')
            : 'All products';
        $monthLabel = $startDate ? \Carbon\Carbon::parse($startDate)->format('M Y') : 'All';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.counter-report', [
            'rows'         => $aggregated['rows'],
            'totals'       => $aggregated['totals'],
            'companyName'  => $companyName,
            'periodLabel'  => $periodLabel,
            'productLabel' => $productLabel,
            'monthLabel'   => $monthLabel,
            'generatedAt'  => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'portrait');

        $fileName = 'Counter_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Display "Pay Later Customers" report:
     * every sale that was recorded with an unpaid balance, regardless of whether the
     * customer has since cleared it. Filtered by sale_date so a month-end view shows
     * everyone who took something on credit that month.
     */
    public function payLater(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status'); // '', 'cleared', 'outstanding'

        $rows = $this->getPayLaterRows($startDate, $endDate, $status);
        $totals = $this->summarizePayLaterRows($rows);

        return view('admin.reports.pay-later', [
            'rows'      => $rows,
            'totals'    => $totals,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'status'    => $status,
        ]);
    }

    /**
     * Fetch and shape pay-later sales for both the screen view and the exporters.
     */
    protected function getPayLaterRows(?string $startDate, ?string $endDate, ?string $status)
    {
        $query = Sale::payLater()->with('salePayments')->orderBy('sale_date', 'desc');

        if ($startDate) {
            $query->whereDate('sale_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('sale_date', '<=', $endDate);
        }
        if ($status === 'cleared') {
            $query->where('pending_amount', '<=', 0.01);
        } elseif ($status === 'outstanding') {
            $query->where('pending_amount', '>', 0.01);
        }

        return $query->get()->map(function (Sale $sale) {
            $originalPayLater = max(0.0, (float) $sale->total_amount - (float) $sale->amount_paid);
            $currentPending   = (float) $sale->pending_amount;
            $isCleared        = $currentPending <= 0.01;

            $clearedOn = null;
            if ($isCleared && $sale->salePayments->isNotEmpty()) {
                $clearedOn = $sale->salePayments->max('payment_date');
            }

            return [
                'id'                  => $sale->id,
                'sale_date'           => $sale->sale_date,
                'seller_name'         => $sale->seller_name ?: '—',
                'seller_contact'      => $sale->seller_contact_number ?: '—',
                'total_amount'        => (float) $sale->total_amount,
                'paid_at_sale'        => (float) $sale->amount_paid,
                'original_pay_later'  => $originalPayLater,
                'current_pending'     => $currentPending,
                'status'              => $isCleared ? 'cleared' : 'outstanding',
                'cleared_on'          => $clearedOn,
                'follow_up_count'     => $sale->salePayments->count(),
            ];
        });
    }

    /**
     * Aggregate totals for the summary cards.
     */
    protected function summarizePayLaterRows($rows): array
    {
        return [
            'sales_count'        => $rows->count(),
            'total_sales'        => (float) $rows->sum('total_amount'),
            'original_pay_later' => (float) $rows->sum('original_pay_later'),
            'cleared_count'      => $rows->where('status', 'cleared')->count(),
            'outstanding_count'  => $rows->where('status', 'outstanding')->count(),
            'outstanding_amount' => (float) $rows->sum('current_pending'),
        ];
    }

    /**
     * Export Pay Later Customers to Excel.
     */
    public function exportPayLater(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status');

        $rows   = $this->getPayLaterRows($startDate, $endDate, $status);
        $totals = $this->summarizePayLaterRows($rows);

        $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
        $periodLabel = $startDate && $endDate
            ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
            : 'All records';
        $statusLabel = $status === 'cleared' ? 'Cleared only' : ($status === 'outstanding' ? 'Outstanding only' : 'All');

        $fileName = 'Pay_Later_Customers_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($rows, $totals, $companyName, $periodLabel, $statusLabel) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $headers = ['Sale Date', 'Seller', 'Contact', 'Total', 'Paid At Sale', 'Original Pay Later', 'Current Pending', 'Status', 'Cleared On'];
            $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

            $sheet->setCellValue('A1', $companyName . ' — Pay Later Customers');
            $sheet->mergeCells("A1:{$lastColLetter}1");
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
            $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2', 'Period: ' . $periodLabel . '   |   Status: ' . $statusLabel . '   |   Generated: ' . now()->format('d M Y H:i'));
            $sheet->mergeCells("A2:{$lastColLetter}2");
            $sheet->getStyle('A2')->getFont()->setItalic(true);
            $sheet->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $headerRow = 4;
            $col = 1;
            foreach ($headers as $h) {
                $sheet->setCellValueByColumnAndRow($col, $headerRow, $h);
                $sheet->getStyleByColumnAndRow($col, $headerRow)->getFont()->setBold(true);
                $sheet->getStyleByColumnAndRow($col, $headerRow)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('E5E7EB');
                $col++;
            }

            $row = $headerRow + 1;
            if ($rows->isEmpty()) {
                $sheet->setCellValueByColumnAndRow(1, $row, 'No pay-later customers in the selected period.');
                $sheet->mergeCells("A{$row}:{$lastColLetter}{$row}");
            } else {
                foreach ($rows as $r) {
                    $sheet->setCellValueByColumnAndRow(1, $row, $r['sale_date']->format('d M Y'));
                    $sheet->setCellValueByColumnAndRow(2, $row, $r['seller_name']);
                    $sheet->setCellValueByColumnAndRow(3, $row, $r['seller_contact']);
                    $sheet->setCellValueByColumnAndRow(4, $row, $r['total_amount']);
                    $sheet->setCellValueByColumnAndRow(5, $row, $r['paid_at_sale']);
                    $sheet->setCellValueByColumnAndRow(6, $row, $r['original_pay_later']);
                    $sheet->setCellValueByColumnAndRow(7, $row, $r['current_pending']);
                    $sheet->setCellValueByColumnAndRow(8, $row, ucfirst($r['status']));
                    $sheet->setCellValueByColumnAndRow(9, $row, $r['cleared_on'] ? $r['cleared_on']->format('d M Y') : '—');
                    $row++;
                }

                // Summary row
                $row++;
                $sheet->setCellValueByColumnAndRow(1, $row, 'TOTAL (' . $totals['sales_count'] . ' sales)');
                $sheet->setCellValueByColumnAndRow(4, $row, $totals['total_sales']);
                $sheet->setCellValueByColumnAndRow(6, $row, $totals['original_pay_later']);
                $sheet->setCellValueByColumnAndRow(7, $row, $totals['outstanding_amount']);
                $sheet->setCellValueByColumnAndRow(8, $row, $totals['cleared_count'] . ' cleared, ' . $totals['outstanding_count'] . ' outstanding');
                $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F3F4F6');
            }

            $widths = [12, 22, 14, 11, 12, 16, 14, 13, 12];
            foreach ($widths as $i => $w) {
                $sheet->getColumnDimensionByColumn($i + 1)->setWidth($w);
            }

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Export Pay Later Customers to PDF.
     */
    public function exportPayLaterPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate   = $request->input('end_date');
        $status    = $request->input('status');

        $rows   = $this->getPayLaterRows($startDate, $endDate, $status);
        $totals = $this->summarizePayLaterRows($rows);

        $companyName = optional(\App\Models\CompanyProfile::first())->company_name ?: 'Parampara';
        $periodLabel = $startDate && $endDate
            ? \Carbon\Carbon::parse($startDate)->format('d M Y') . ' to ' . \Carbon\Carbon::parse($endDate)->format('d M Y')
            : 'All records';
        $statusLabel = $status === 'cleared' ? 'Cleared only' : ($status === 'outstanding' ? 'Outstanding only' : 'All');
        $monthLabel = $startDate ? \Carbon\Carbon::parse($startDate)->format('M Y') : 'All';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.pay-later-report', [
            'rows'         => $rows,
            'totals'       => $totals,
            'companyName'  => $companyName,
            'periodLabel'  => $periodLabel,
            'statusLabel'  => $statusLabel,
            'monthLabel'   => $monthLabel,
            'generatedAt'  => now()->format('d M Y H:i'),
        ])->setPaper('a4', 'landscape');

        $fileName = 'Pay_Later_Customers_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Display stock report
     */
    public function stock()
    {
        $products = Product::with('purchaseItems', 'saleItems', 'purchaseReturns', 'saleReturns')->get();

        // Calculate stock data for each product
        $stockData = $products->map(function ($product) {
            $totalPurchase = $product->purchaseItems->sum('quantity');
            $totalSales = $product->saleItems->sum('quantity');
            $purchaseReturn = $product->purchaseReturns->sum('quantity');
            $saleReturn = $product->saleReturns->sum('quantity');
            $availableStock = $totalPurchase - $purchaseReturn - $totalSales + $saleReturn;

            return [
                'product' => $product,
                'total_purchase' => $totalPurchase,
                'total_sales' => $totalSales,
                'purchase_return' => $purchaseReturn,
                'sale_return' => $saleReturn,
                'available_stock' => max(0, $availableStock),
            ];
        });

        $totalProducts = $products->count();
        $activeProducts = $products->where('is_active', true)->count();
        $inactiveProducts = $totalProducts - $activeProducts;

        return view('admin.reports.stock', compact(
            'stockData',
            'totalProducts',
            'activeProducts',
            'inactiveProducts'
        ));
    }
}
