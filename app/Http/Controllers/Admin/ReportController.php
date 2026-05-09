<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sale;
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

        // Get all products for grouping
        $products = Product::all();

        // Calculate totals
        $totalSales = $sales->sum('total_amount');
        $totalQuantity = $sales->flatMap->items->sum('quantity');
        $paidAmount = $sales->sum('total_amount') - $sales->sum('pending_amount');
        $pendingAmount = $sales->sum('pending_amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate ?? now()->startOfMonth(), $endDate ?? now()])->sum('amount');

        // Calculate cash and online sales separately
        $cashSales = $sales->where('payment_mode', 'cash')->sum('total_amount');
        $onlineSales = $sales->whereIn('payment_mode', config('payment.sale_modes_online'))->sum('total_amount');
        $mixSales = $sales->where('payment_mode', 'mix')->sum('total_amount');

        // Add cash and online amounts from mix payments to respective totals
        $cashSales += $sales->where('payment_mode', 'mix')->sum('cash_amount');
        $onlineSales += $sales->where('payment_mode', 'mix')->sum('online_amount');

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

        // Create Excel file
        $fileName = 'Sales_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($sales, $products, $startDate, $endDate) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $monthYear = $startDate ? date('M-y', strtotime($startDate)) : 'All';
            $sheet->setCellValue('A1', $monthYear);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Build dynamic headers - Date + Product Codes + Sales/Return/Expense columns
            $headers = ['Date'];
            foreach ($products as $product) {
                $headers[] = $product->product_code;
            }
            $headers = array_merge($headers, ['Online', 'Cash', 'Return', 'Return Details', 'Total', 'Expense', 'Exp.Detail', 'Total']);

            // Write headers
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 3, $header);
                $sheet->getStyleByColumnAndRow($col, 3)->getFont()->setBold(true);
                $col++;
            }

            // Calculate column positions for sales/expense data
            $productCount = $products->count();
            $onlineCol = 2 + $productCount;
            $cashCol = $onlineCol + 1;
            $returnCol = $cashCol + 1;
            $returnDetailsCol = $returnCol + 1;
            $totalCol = $returnDetailsCol + 1;
            $expenseCol = $totalCol + 1;
            $expenseDetailsCol = $expenseCol + 1;
            $finalTotalCol = $expenseDetailsCol + 1;

            // Group sales by date
            $salesByDate = $sales->groupBy(function ($sale) {
                return $sale->sale_date->format('d-m-Y');
            });

            // Sort dates in ascending order
            /*$salesByDate = $salesByDate->sortKeys(function ($a, $b) {
                return strtotime(str_replace('-', '/', $a)) <=> strtotime(str_replace('-', '/', $b));
            });*/

            $row = 4;
            $totalCash = 0;
            $totalOnline = 0;
            $totalExpense = 0;

            // Initialize product-wise totals
            $productTotals = [];
            foreach ($products as $product) {
                $productTotals[$product->product_code] = 0;
            }

            foreach ($salesByDate as $date => $dateSales) {
                // Get product quantities for this date
                $productQtys = [];
                foreach ($products as $product) {
                    $productQtys[$product->product_code] = 0;
                }

                $dateCashAmount = 0;
                $dateOnlineAmount = 0;
                $dateReturnAmount = 0;
                $dateExpenseAmount = 0;

                foreach ($dateSales as $sale) {
                    // Separate cash and online sales
                    if ($sale->payment_mode === 'cash') {
                        $dateCashAmount += $sale->total_amount;
                    } elseif (in_array($sale->payment_mode, config('payment.sale_modes_online'))) {
                        $dateOnlineAmount += $sale->total_amount;
                    } elseif ($sale->payment_mode === 'mix') {
                        // For mix payments, add cash and online amounts separately
                        $dateCashAmount += $sale->cash_amount ?? 0;
                        $dateOnlineAmount += $sale->online_amount ?? 0;
                    }

                    foreach ($sale->items as $item) {
                        $code = $item->product?->product_code ?? '';
                        if ($code && isset($productQtys[$code])) {
                            $productQtys[$code] += $item->quantity;
                        }
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

                // Write expense (dynamic column)
                $sheet->setCellValueByColumnAndRow($expenseCol, $row, $dateExpenseAmount > 0 ? $dateExpenseAmount : '');

                // Write expense details (dynamic column)
                $expenseDetails = '';
                foreach ($dateExpenses as $expense) {
                    $categoryName = $expense->expenseCategory ? $expense->expenseCategory->name : 'Other';
                    $label = $expense->notes ? $categoryName . ' - ' . $expense->notes : $categoryName;
                    $expenseDetails .= number_format($expense->amount, 0) . '/- ' . $label . '; ';
                }
                $sheet->setCellValueByColumnAndRow($expenseDetailsCol, $row, trim($expenseDetails));

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

            // Auto-size columns dynamically based on total column count
            $totalColumns = $finalTotalCol;
            for ($i = 1; $i <= $totalColumns; $i++) {
                $sheet->getColumnDimensionByColumn($i)->setAutoSize(true);
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
