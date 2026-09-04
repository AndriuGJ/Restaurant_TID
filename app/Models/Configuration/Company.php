<?php

namespace App\Models\Configuration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'commercial_name',
        'phone',
        'commercial_address',
        'ruc',
        'social_reason',
        'fiscal_address',
        'ubigeo_id',
        'logo',
        'sol_user',
        'sol_password',
        'digital_certificate_path',
    ];

    protected $hidden = [
        'sol_password',
        'digital_certificate_path',
    ];

    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public function ubigeo()
    {
        return $this->belongsTo(Ubigeo::class);
    }

    public function sunatConfigs()
    {
        return $this->hasMany(SunatConfig::class);
    }
}
