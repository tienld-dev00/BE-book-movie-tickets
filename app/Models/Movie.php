<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'duration',
        'age_limit',
        'release_date',
        'status',
        'slug',
        'category_id',
    ];

    /**
     * Get the showtimes for the movie.
     */
    public function showtimes(): HasMany
    {
        return $this->hasMany(Showtime::class);
    }
}
