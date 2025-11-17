<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'account_id',
        'amount',
        'teller_shift_id',
    ];

    protected $casts = [
        'amount' => 'integer',
    ];

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function tellerShift()
    {
        return $this->belongsTo(TellerShift::class);
    }
}
