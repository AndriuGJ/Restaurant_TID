<?php

namespace App\Models\Inventory;

use App\Models\Kardex\KardexMovement;
use App\Models\Purchases\PurchaseDetail;
use App\Models\Sales\SaleDetail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'cost_price',
        'sale_price',
        'image_url',
        'stock',
        'unit_of_measure',
        'product_category_id',
        'purchase_category_id',
        'type',
        'is_pos_item',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'stock' => 'decimal:2',
            'is_pos_item' => 'boolean',
            'status' => 'boolean',
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class);
    }

    public function purchaseCategory()
    {
        return $this->belongsTo(PurchaseCategory::class);
    }

    public function ingredients()
    {
        return $this->hasMany(ProductIngredient::class, 'dish_id');
    }

    public function usedAsIngredientIn()
    {
        return $this->hasMany(ProductIngredient::class, 'ingredient_id');
    }

    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function saleDetails()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function kardexMovements()
    {
        return $this->hasMany(KardexMovement::class);
    }
}
