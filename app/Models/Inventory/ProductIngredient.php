<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductIngredient extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'dish_id',
        'ingredient_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
        ];
    }

    public function dish()
    {
        return $this->belongsTo(Product::class, 'dish_id');
    }

    public function ingredient()
    {
        return $this->belongsTo(Product::class, 'ingredient_id');
    }
}
