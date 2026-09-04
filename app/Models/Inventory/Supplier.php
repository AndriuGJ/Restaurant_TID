<?php

namespace App\Models\Inventory;

use App\Models\Purchases\Purchase;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'ruc',
        'social_reason',
        'phone',
        'contact_person',
        'email',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
