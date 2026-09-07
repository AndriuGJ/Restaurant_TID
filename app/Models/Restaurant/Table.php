<?php

namespace App\Models\Restaurant;

use App\Models\Sales\Sale;
use App\Models\Sales\SaleTable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'hall_id',
        'name',
        'shape',
        'pos_x',
        'pos_y',
        'status',
    ];

    public function getRouteKeyName(): string
    {
        return 'name';
    }

    public function hall()
    {
        return $this->belongsTo(Hall::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function saleTables()
    {
        return $this->hasMany(SaleTable::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function activeReservation()
    {
        return $this->hasOne(Reservation::class)
            ->where('status', 'active')
            ->latestOfMany();
    }
}
