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
        'rejection_reason',
        'rejected_at',
        'accepted_at',
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
        'rejected_at' => 'datetime',
        'accepted_at' => 'datetime',
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

    /**
     * Check if shift is pending teller acceptance
     */
    public function isPendingAcceptance()
    {
        return $this->status === 'pending_teller_acceptance';
    }

    /**
     * Check if shift can be accepted by teller
     */
    public function canAccept()
    {
        return $this->status === 'pending_teller_acceptance' && $this->teller_id === auth()->id();
    }

    /**
     * Check if shift can be rejected by teller
     */
    public function canReject()
    {
        return $this->status === 'pending_teller_acceptance' && $this->teller_id === auth()->id();
    }

    /**
     * Check if shift is rejected
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if shift is pending teller confirmation
     */
    public function isPendingConfirmation()
    {
        return $this->status === 'pending_teller_confirmation';
    }

    /**
     * Check if shift can be confirmed by teller
     */
    public function canConfirm()
    {
        return $this->status === 'pending_teller_confirmation' && $this->teller_id === auth()->id();
    }

    /**
     * Check if shift can be reopened by treasurer
     */
    public function canReopen()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if shift allows transactions
     */
    public function allowsTransactions()
    {
        return $this->status === 'open' && !$this->isRejected();
    }
}
