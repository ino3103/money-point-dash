<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TellerShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'teller_id',
        'treasurer_id',
        'opened_at',
        'closed_at',
        'status',
        'opening_cash',
        'opening_floats',
        'closing_cash',
        'closing_floats',
        'expected_closing_cash',
        'expected_closing_floats',
        'variance_cash',
        'variance_floats',
        'notes',
    ];

    protected $casts = [
        'opening_cash' => 'integer',
        'opening_floats' => 'array',
        'closing_cash' => 'integer',
        'closing_floats' => 'array',
        'expected_closing_cash' => 'integer',
        'expected_closing_floats' => 'array',
        'variance_cash' => 'integer',
        'variance_floats' => 'array',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function teller()
    {
        return $this->belongsTo(User::class, 'teller_id');
    }

    public function treasurer()
    {
        return $this->belongsTo(User::class, 'treasurer_id');
    }

    public function transactions()
    {
        return $this->hasMany(MoneyPointTransaction::class);
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }

    /**
     * Check if shift is open
     */
    public function isOpen()
    {
        return $this->status === 'open';
    }

    /**
     * Check if shift can be submitted
     */
    public function canSubmit()
    {
        return $this->status === 'open';
    }

    /**
     * Check if shift can be verified
     */
    public function canVerify()
    {
        return $this->status === 'submitted';
    }
}
