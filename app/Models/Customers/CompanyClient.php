<?php

namespace App\Models\Customers;

use App\Models\Configuration\DocumentType;
use App\Models\Sales\Sale;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyClient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruc',
        'social_reason',
        'phone',
        'contact_person',
        'document_type_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'boolean',
        ];
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
