<?php

namespace App\Models\Configuration;

use App\Models\Configuration\Company as ConfigurationCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubigeo extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'department',
        'province',
        'district',
        'code',
    ];

    public function companies()
    {
        return $this->hasMany(ConfigurationCompany::class);
    }
}
