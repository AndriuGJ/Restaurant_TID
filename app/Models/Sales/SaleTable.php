<?php

namespace App\Models\Sales;

use App\Models\Restaurant\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaleTable extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'sale_id',
        'table_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }
}
