<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'movies';

    public function Showtime()
    {
        return $this->hasMany('App\Models\Showtime', 'movie_id');
    }
}
