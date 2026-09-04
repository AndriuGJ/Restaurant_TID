<?php

namespace App\Models\Restaurant;

use App\Models\Sales\Sale;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashRegisterSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_register_id',
        'shift_id',
        'user_opening_id',
        'user_closing_id',
        'opening_amount',
        'closing_amount',
        'status',
        'opened_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_amount' => 'decimal:2',
            'closing_amount' => 'decimal:2',
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function cashRegister()
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function userOpening()
    {
        return $this->belongsTo(User::class, 'user_opening_id');
    }

    public function userClosing()
    {
        return $this->belongsTo(User::class, 'user_closing_id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
