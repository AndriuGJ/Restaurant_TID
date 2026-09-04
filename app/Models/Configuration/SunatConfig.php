<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SunatConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'start_date',
        'end_date',
        'status',
        'max_receipts',
        'used_receipts',
        'card_surcharge_percentage',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'card_surcharge_percentage' => 'decimal:2',
        ];
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
