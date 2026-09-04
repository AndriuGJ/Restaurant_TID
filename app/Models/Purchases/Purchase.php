<?php

namespace App\Models\Purchases;

use App\Models\Configuration\DocumentType;
use App\Models\Inventory\Supplier;
use App\Models\Kardex\KardexMovement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'user_id',
        'purchase_type',
        'document_type_id',
        'series',
        'number',
        'purchase_date',
        'subtotal',
        'tax',
        'total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function kardexMovements()
    {
        return $this->morphMany(KardexMovement::class, 'related_document');
    }
}
