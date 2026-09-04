<?php

namespace App\Models\Sales;

use App\Models\Configuration\DocumentType;
use App\Models\Kardex\KardexMovement;
use App\Models\Restaurant\CashRegisterSession;
use App\Models\Restaurant\DeliveryProvider;
use App\Models\Restaurant\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cash_register_session_id',
        'table_id',
        'guests',
        'sale_type',
        'is_takeaway',
        'delivery_provider_id',
        'delivery_person_name',
        'clientable_id',
        'clientable_type',
        'document_type_id',
        'series',
        'number',
        'subtotal',
        'tax',
        'total',
        'change',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_takeaway' => 'boolean',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'change' => 'decimal:2',
        ];
    }
    // public function getRouteKeyName(): string
    // {
    //     return 'name';
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cashRegisterSession()
    {
        return $this->belongsTo(CashRegisterSession::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function deliveryProvider()
    {
        return $this->belongsTo(DeliveryProvider::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function clientable()
    {
        return $this->morphTo();
    }

    public function saleTables()
    {
        return $this->hasMany(SaleTable::class);
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public function kardexMovements()
    {
        return $this->morphMany(KardexMovement::class, 'related_document');
    }
}
