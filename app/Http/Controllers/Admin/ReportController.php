<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sell;
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
        $totalSales = Sell::sum('total_amount');
        $totalPurchases = Purchase::sum('total_amount');
        $totalProfit = $totalSales - $totalPurchases;
        $pendingPayments = Sell::where('payment_status', '!=', 'paid')->sum('pending_amount');
        $totalProducts = Product::count();

        return view('admin.reports.index', compact(
            'totalSales',
            'totalPurchases',
            'totalProfit',
            'pendingPayments',
            'totalProducts'
        ));
    }

    /**
     * Display sales report
     */
    public function sales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sell::with('items.product', 'returns');

        // Apply date filter if provided
        if ($startDate && $endDate) {
            $query->whereBetween('sell_date', [$startDate, $endDate]);
        }

        $sells = $query->orderBy('sell_date', 'desc')->get();

        // Get all products for grouping
        $products = Product::all();

        // Calculate totals
        $totalSales = $sells->sum('total_amount');
        $totalQuantity = $sells->flatMap->items->sum('quantity');
        $paidAmount = $sells->sum('amount_paid');
        $pendingAmount = $sells->sum('pending_amount');
        $totalExpenses = Expense::whereBetween('expense_date', [$startDate ?? now()->startOfMonth(), $endDate ?? now()])->sum('amount');

        // Calculate cash and online sales separately
        $cashSales = $sells->where('payment_mode', 'cash')->sum('total_amount');
        $onlineSales = $sells->whereIn('payment_mode', ['upi', 'qr'])->sum('total_amount');
        $mixSales = $sells->where('payment_mode', 'mix')->sum('total_amount');

        // Calculate product-wise quantity breakdown
        $quantityByProduct = [];
        foreach ($sells as $sell) {
            foreach ($sell->items as $item) {
                $productCode = $item->product->product_code;
                if (!isset($quantityByProduct[$productCode])) {
                    $quantityByProduct[$productCode] = 0;
                }
                $quantityByProduct[$productCode] += $item->quantity;
            }
        }
        // Sort by product code for consistent display
        ksort($quantityByProduct);

        return view('admin.reports.sales', compact(
            'sells',
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
            'endDate'
        ));
    }

    /**
     * Display purchases report
     */
    public function purchases()
    {
        $purchases = Purchase::with('items')->get();
        $totalPurchases = $purchases->sum('total_amount');
        $totalItems = $purchases->flatMap->items->count();

        return view('admin.reports.purchases', compact(
            'purchases',
            'totalPurchases',
            'totalItems'
        ));
    }

    /**
     * Export sales report to Excel
     */
    public function exportSales(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = Sell::with('items.product', 'returns');

        if ($startDate && $endDate) {
            $query->whereBetween('sell_date', [$startDate, $endDate]);
        }

        $sells = $query->orderBy('sell_date', 'desc')->get();
        $products = Product::orderBy('id')->get();

        // Create Excel file
        $fileName = 'Sales_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($sells, $products, $startDate, $endDate) {
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

            // Group sells by date
            $sellsByDate = $sells->groupBy(function ($sell) {
                return $sell->sell_date->format('d-m-Y');
            });

            // Sort dates in ascending order
            /*$sellsByDate = $sellsByDate->sortKeys(function ($a, $b) {
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

            foreach ($sellsByDate as $date => $dateSells) {
                // Get product quantities for this date
                $productQtys = [];
                foreach ($products as $product) {
                    $productQtys[$product->product_code] = 0;
                }

                $dateCashAmount = 0;
                $dateOnlineAmount = 0;
                $dateReturnAmount = 0;
                $dateExpenseAmount = 0;

                foreach ($dateSells as $sell) {
                    // Separate cash and online sales
                    if ($sell->payment_mode === 'cash') {
                        $dateCashAmount += $sell->total_amount;
                    } elseif (in_array($sell->payment_mode, ['upi', 'qr'])) {
                        $dateOnlineAmount += $sell->total_amount;
                    } elseif ($sell->payment_mode === 'mix') {
                        // For mix payments, we need to track both - for now add to cash
                        $dateCashAmount += $sell->total_amount;
                    }

                    foreach ($sell->items as $item) {
                        $code = $item->product->product_code;
                        if (isset($productQtys[$code])) {
                            $productQtys[$code] += $item->quantity;
                        }
                    }
                }

                // Get returns for this date
                foreach ($dateSells as $sell) {
                    foreach ($sell->returns as $return) {
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
                foreach ($dateSells as $sell) {
                    foreach ($sell->returns as $return) {
                        $returnDetails .= $return->product->product_code . ': ' . $return->quantity . '; ';
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
                    $description = $expense->description ? $expense->description : $categoryName;
                    $expenseDetails .= number_format($expense->amount, 0) . '/- ' . $description . '; ';
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

            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $fileName);
    }

    /**
     * Display stock report
     */
    public function stock()
    {
        $products = Product::with('purchaseItems', 'sellItems', 'purchaseReturns', 'sellReturns')->get();

        // Calculate stock data for each product
        $stockData = $products->map(function ($product) {
            $totalPurchase = $product->purchaseItems->sum('quantity');
            $totalSell = $product->sellItems->sum('quantity');
            $purchaseReturn = $product->purchaseReturns->sum('quantity');
            $sellReturn = $product->sellReturns->sum('quantity');
            $availableStock = $totalPurchase - $purchaseReturn - $totalSell + $sellReturn;

            return [
                'product' => $product,
                'total_purchase' => $totalPurchase,
                'total_sell' => $totalSell,
                'purchase_return' => $purchaseReturn,
                'sell_return' => $sellReturn,
                'available_stock' => max(0, $availableStock),
            ];
        });

        $totalProducts = $products->count();

        return view('admin.reports.stock', compact(
            'stockData',
            'totalProducts'
        ));
    }
}
