<?php

namespace App\Interfaces\Showtime;

use App\Interfaces\CrudRepositoryInterface;

interface ShowtimeRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * get list day have showtime
     *
     * @param  int $movie_id
     * @return array
     */
    public function showDate($movie_id);

    /**
     * get list showtime by day
     *
     * @param  Request $data
     * @return Collection
     */
    public function getShowtimeByDate($data);

    /**
     * show showtime by id 
     *
     * @param  int $showtime_id
     * @return Resource
     */
    public function getShowtime($showtime_id);

    /**
     * Get list showtime by query condition
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getShowtimeByQuery(array $data);
}
