<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    use HasFactory;

    protected $table = 'finance_transactions';

    protected $fillable = [
        'type',          // income, expense, refund
        'category',      // sales_app, sales_pos, refund, operational_rent, etc.
        'amount',
        'reference_id',  // order number or invoice number
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];
}
