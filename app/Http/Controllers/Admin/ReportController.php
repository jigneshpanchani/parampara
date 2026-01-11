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
        $products = Product::all();

        // Create Excel file
        $fileName = 'Sales_Report_' . ($startDate ? date('M-Y', strtotime($startDate)) : 'All') . '.xlsx';

        return response()->streamDownload(function () use ($sells, $products, $startDate, $endDate) {
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Set title
            $monthYear = $startDate ? date('M-y', strtotime($startDate)) : 'All';
            $sheet->setCellValue('A1', $monthYear);
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

            // Set headers - Updated to include separate Online and Cash columns
            $headers = ['Date', 'ZAR', 'DOL', 'TIN', 'KEN', 'BOTTLE', 'B-Tal', 'W-Tal', 'Sing', 'Marchu', 'Online', 'Cash', 'Return', 'Return Details', 'Total', 'Expense', 'Exp.Detail', 'Total'];
            $col = 1;
            foreach ($headers as $header) {
                $sheet->setCellValueByColumnAndRow($col, 3, $header);
                $sheet->getStyleByColumnAndRow($col, 3)->getFont()->setBold(true);
                $col++;
            }

            // Group sells by date
            $sellsByDate = $sells->groupBy(function ($sell) {
                return $sell->sell_date->format('d-m-Y');
            });

            $row = 4;
            $totalCash = 0;
            $totalOnline = 0;
            $totalExpense = 0;

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
                $dateExpenses = Expense::whereDate('expense_date', $date)->get();
                foreach ($dateExpenses as $expense) {
                    $dateExpenseAmount += $expense->amount;
                }

                // Write date
                $sheet->setCellValueByColumnAndRow(1, $row, $date);

                // Write product quantities
                $col = 2;
                foreach ($products as $product) {
                    $code = $product->product_code;
                    if (isset($productQtys[$code]) && $productQtys[$code] > 0) {
                        $sheet->setCellValueByColumnAndRow($col, $row, $productQtys[$code]);
                    }
                    $col++;
                }

                // Write online amount (column 11)
                $sheet->setCellValueByColumnAndRow(11, $row, $dateOnlineAmount > 0 ? $dateOnlineAmount : '');

                // Write cash amount (column 12)
                $sheet->setCellValueByColumnAndRow(12, $row, $dateCashAmount > 0 ? $dateCashAmount : '');

                // Write return info (column 13)
                $sheet->setCellValueByColumnAndRow(13, $row, $dateReturnAmount > 0 ? $dateReturnAmount : '');

                // Write return details (column 14)
                $returnDetails = '';
                foreach ($dateSells as $sell) {
                    foreach ($sell->returns as $return) {
                        $returnDetails .= $return->product->product_code . ': ' . $return->quantity . '; ';
                    }
                }
                $sheet->setCellValueByColumnAndRow(14, $row, trim($returnDetails));

                // Write total (column 15) - Sales - Returns
                $total = ($dateCashAmount + $dateOnlineAmount) - $dateReturnAmount;
                $sheet->setCellValueByColumnAndRow(15, $row, $total);

                // Write expense (column 16)
                $sheet->setCellValueByColumnAndRow(16, $row, $dateExpenseAmount > 0 ? $dateExpenseAmount : '');

                // Write expense details (column 17)
                $expenseDetails = '';
                foreach ($dateExpenses as $expense) {
                    $expenseDetails .= $expense->description . '; ';
                }
                $sheet->setCellValueByColumnAndRow(17, $row, trim($expenseDetails));

                // Write final total (column 18) - Sales - Returns - Expenses
                $finalTotal = $total - $dateExpenseAmount;
                $sheet->setCellValueByColumnAndRow(18, $row, $finalTotal);

                $totalCash += $dateCashAmount;
                $totalOnline += $dateOnlineAmount;
                $totalExpense += $dateExpenseAmount;
                $row++;
            }

            // Auto-size columns
            foreach (range('A', 'R') as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
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
