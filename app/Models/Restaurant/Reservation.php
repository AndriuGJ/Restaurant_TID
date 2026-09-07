<?php

namespace App\Models\Restaurant;

use App\Models\User;
use Database\Factories\Restaurant\ReservationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    protected $fillable = [
        'table_id',
        'customer_name',
        'people_count',
        'user_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'people_count' => 'integer',
        ];
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
