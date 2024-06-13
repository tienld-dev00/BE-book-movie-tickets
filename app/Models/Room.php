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
        'name'
    ];

    public function Showtime()
    {
        return $this->hasMany('App\Models\Showtime', 'room_id');
    }

    public function Seat()
    {
        return $this->hasMany('App\Models\Seat', 'room_id');
    }
}
