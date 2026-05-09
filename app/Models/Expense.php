<?php

namespace App\Models;

use App\Traits\ActivityTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    use ActivityTrait;

    protected static $logName = 'Expense';

    public function getLogDescription(string $event): string
    {
        $label = $this->expenseCategory?->name ?? '';
        return "Expense <strong>{$label}</strong> (₹{$this->amount}) has been {$event} by";
    }

    protected static $logAttributes = ['expense_date', 'category_id', 'amount', 'payment_method', 'notes'];

    protected $fillable = [
        'expense_date',
        'category_id',
        'amount',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'expense_date' => 'date',
    ];

    /**
     * Get the category for this expense
     */
    public function expenseCategory()
    {
        return $this->belongsTo(ExpenseCategory::class, 'category_id');
    }
}
