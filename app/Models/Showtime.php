<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'showtimes';

    public function Movie()
    {
        return $this->belongsTo('App\Models\Movie', 'movie_id');
    }

    public function Room()
    {
        return $this->belongsTo('App\Models\Room', 'room_id');
    }

    protected $fillable = [
        'id',
        'start_time',
        'end_time',
        'movie_id',
        'room_id',
        'price',
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
