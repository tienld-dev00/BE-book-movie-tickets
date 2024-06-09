<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Showtime extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'price',
        'movie_id',
        'room_id',
        'status',
    ];

    /**
     * Get the orders for the showtime.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
