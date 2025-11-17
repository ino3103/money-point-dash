<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_no',
        'gender',
        'username',
        'status',
        'profile_picture'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }

    public function tellerShifts()
    {
        return $this->hasMany(TellerShift::class, 'teller_id');
    }

    public function allocatedShifts()
    {
        return $this->hasMany(TellerShift::class, 'treasurer_id');
    }

    public function moneyPointTransactions()
    {
        return $this->hasMany(MoneyPointTransaction::class);
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'to_user_id');
    }
}
