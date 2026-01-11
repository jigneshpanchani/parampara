<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Expense;
use Illuminate\Support\Facades\DB;

// Check database structure
echo "=== EXPENSES TABLE STRUCTURE ===\n";
$columns = DB::select("DESCRIBE expenses");
foreach ($columns as $col) {
    echo "{$col->Field} ({$col->Type})\n";
}

echo "\n=== EXPENSE CATEGORIES TABLE ===\n";
$categories = DB::table('expense_categories')->get();
echo "Total categories: " . count($categories) . "\n";
foreach ($categories as $cat) {
    echo "ID: {$cat->id}, Name: {$cat->name}\n";
}

echo "\n=== EXPENSES DATA ===\n";
$expenses = DB::table('expenses')->limit(5)->get();
echo "Total expenses: " . count($expenses) . "\n";
foreach ($expenses as $exp) {
    echo "ID: {$exp->id}, category_id: {$exp->category_id}, category: {$exp->category}\n";
}

echo "\n=== EXPENSES WITH RELATIONSHIP ===\n";
$expensesWithCat = Expense::with('category')->limit(5)->get();
foreach ($expensesWithCat as $exp) {
    $catName = $exp->category ? $exp->category->name : 'NULL';
    echo "ID: {$exp->id}, category_id: {$exp->category_id}, category_name: {$catName}\n";
}

