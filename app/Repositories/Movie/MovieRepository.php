<?php

namespace App\Repositories\Movie;

use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Models\Movie;
use App\Repositories\BaseRepository;
use App\Enums\MovieStatus;

class MovieRepository extends BaseRepository implements MovieRepositoryInterface
{
    public function __construct(Movie $movie)
    {
        $this->model = $movie;
    }

    /**
     * show showtime by slug 
     *
     * @param  int $slug
     * @return Resource
     */
    public function getMovie($slug)
    {
        $showtime = $this->model
            ->where('slug', $slug)
            ->where('status', MovieStatus::SHOW)
            ->first();

        return $showtime;
    }
}
