<?php

namespace App\Models\Configuration;

use App\Models\Sales\SalePayment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
