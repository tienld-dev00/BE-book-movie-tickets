<?php

namespace App\Repositories\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Models\Showtime;
use App\Repositories\BaseRepository;

class ShowtimeRepository extends BaseRepository implements ShowtimeRepositoryInterface
{
    public function __construct(Showtime $showtime)
    {
        $this->model = $showtime;
    }
}
