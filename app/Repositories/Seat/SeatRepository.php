<?php

namespace App\Repositories\Seat;

use App\Interfaces\Seat\SeatRepositoryInterface;
use App\Models\Seat;
use App\Repositories\BaseRepository;

class SeatRepository extends BaseRepository implements SeatRepositoryInterface
{
    public function __construct(Seat $room)
    {
        $this->model = $room;
    }

    /**
     * Create multiple seats
     * 
     * @param array $data
     * 
     * @return boolean
     */
    public function createMultiple(array $data)
    {
        return $this->model->insert($data);
    }
}
