<?php

namespace App\Models\Customers;

use App\Models\Configuration\DocumentType;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'reference_address',
        'document_type_id',
        'document_number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
    }
    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public function documentType()
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function sales()
    {
        return $this->morphMany(Sale::class, 'clientable');
    }
}
