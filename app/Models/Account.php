<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'branch_id',
        'account_type',
        'provider',
        'balance',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'integer',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lines()
    {
        return $this->hasMany(TransactionLine::class);
    }

    /**
     * Get display balance (for float accounts, show absolute value)
     */
    public function getDisplayBalanceAttribute()
    {
        if ($this->account_type === 'float') {
            return abs($this->balance);
        }
        return $this->balance;
    }
}
