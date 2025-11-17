<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'account_id',
        'amount',
        'description',
        'balance_after',
    ];

    protected $casts = [
        'amount' => 'integer',
        'balance_after' => 'integer',
    ];

    public function transaction()
    {
        return $this->belongsTo(MoneyPointTransaction::class, 'transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
