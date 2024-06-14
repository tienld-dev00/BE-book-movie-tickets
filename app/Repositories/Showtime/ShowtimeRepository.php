<?php

namespace App\Repositories\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Models\Showtime;
use App\Repositories\BaseRepository;
use Carbon\Carbon;
use App\Enums\ShowtimeStatus;

class ShowtimeRepository extends BaseRepository implements ShowtimeRepositoryInterface
{
    public function __construct(Showtime $showtime)
    {
        $this->model = $showtime;
    }

    /**
     * get list day have showtime
     *
     * @param  int $movie_id
     * @return array
     */
    public function showDate($movie_id)
    {
        $now = Carbon::now();
        //today if showtime after now
        $allDay = $this->model
            ->where('movie_id', $movie_id)
            ->where('status', ShowtimeStatus::SHOW)
            ->where('start_time', '>', $now)
            ->selectRaw('DATE(start_time) AS date')
            ->distinct()
            ->pluck('date')
            ->sort();

        return $allDay;
    }

    /**
     * get list showtime by day
     *
     * @param  Request $data
     * @return Collection
     */
    public function getShowtimeByDate($data)
    {
        $today = Carbon::today();
        $now = Carbon::now();

        $showtimes = $this->model->select('*')
            ->where('movie_id', $data['movie_id'])
            ->where('status', ShowtimeStatus::SHOW)
            ->whereDate('start_time', $data['date'])
            ->where('start_time', '>', $now)
            ->get();

        return $showtimes;
    }

    /**
     * show showtime by id 
     *
     * @param  int $showtime_id
     * @return Resource
     */
    public function getShowtime($showtime_id)
    {
        $showtime = $this->model
            ->where('id', $showtime_id)
            ->where('status', ShowtimeStatus::SHOW)
            ->first();

        return $showtime;
    }
}
