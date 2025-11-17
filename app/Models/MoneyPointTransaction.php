<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MoneyPointTransaction extends Model
{
    use HasFactory;

    protected $table = 'money_point_transactions';

    protected $fillable = [
        'uuid',
        'type',
        'reference',
        'teller_shift_id',
        'user_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->uuid)) {
                $transaction->uuid = (string) Str::uuid();
            }
        });
    }

    public function tellerShift()
    {
        return $this->belongsTo(TellerShift::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lines()
    {
        return $this->hasMany(TransactionLine::class, 'transaction_id');
    }

    /**
     * Verify that transaction lines sum to zero
     */
    public function verifyBalance()
    {
        $sum = $this->lines()->sum('amount');
        return $sum === 0;
    }
}
