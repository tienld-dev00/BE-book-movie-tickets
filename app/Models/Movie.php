<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes, Sluggable;

    protected $table = 'movies';

    protected $fillable = [
        'name',
        'release_date',
        'category_id',
        'age_limit',
        'duration',
        'description',
        'status',
        'image',
        'trailer',
        'slug',
    ];

    public function Showtime()
    {
        return $this->hasMany('App\Models\Showtime', 'movie_id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }
}
