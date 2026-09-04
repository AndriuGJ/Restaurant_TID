<?php

namespace App\Models\Kardex;

use App\Models\Inventory\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KardexMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'movement_type',
        'quantity_in',
        'quantity_out',
        'balance',
        'related_document_id',
        'related_document_type',
    ];

    protected function casts(): array
    {
        return [
            'quantity_in' => 'decimal:2',
            'quantity_out' => 'decimal:2',
            'balance' => 'decimal:2',
        ];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function relatedDocument()
    {
        return $this->morphTo();
    }
}
