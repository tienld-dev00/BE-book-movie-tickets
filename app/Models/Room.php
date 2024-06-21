<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'rooms';

    protected $fillable = [
        'id',
        'name',
        'row_number',
        'column_number'
    ];

    public function Showtime()
    {
        return $this->hasMany('App\Models\Showtime', 'room_id');
    }

    public function Seat()
    {
        return $this->hasMany('App\Models\Seat', 'room_id');
    }

    /**
     * Get the seats for room.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
