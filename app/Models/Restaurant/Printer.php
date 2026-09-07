<?php

namespace App\Models\Restaurant;

use Database\Factories\Restaurant\PrinterFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Printer extends Model
{
    /** @use HasFactory<PrinterFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'connection_type',
        'ip_address',
        'port',
        'queue_name',
        'send_raw',
        'is_default',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'port' => 'integer',
            'send_raw' => 'boolean',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
