<?php

namespace App\Models\Configuration;

use App\Models\Customers\CompanyClient;
use App\Models\Customers\Customer;
use App\Models\Purchases\Purchase;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'nomenclature',
        'character_limit',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function companyClients()
    {
        return $this->hasMany(CompanyClient::class);
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
