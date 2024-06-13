<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seat extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'seats';

    protected $fillable = [
        'id',
        'name',
        'room_id'
    ];

    public function Room()
    {
        return $this->belongsTo('App\Models\Room', 'room_id');
    }
}
