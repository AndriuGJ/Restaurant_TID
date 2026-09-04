<?php

namespace App\Models;

use App\Models\Purchases\Purchase;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Sales\Sale;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'dni',
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'cargo',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function cashRegisterSessionsOpened()
    {
        return $this->hasMany(CashRegisterSession::class, 'user_opening_id');
    }

    public function cashRegisterSessionsClosed()
    {
        return $this->hasMany(CashRegisterSession::class, 'user_closing_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
