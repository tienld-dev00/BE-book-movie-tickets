<?php

namespace App\Interfaces\Seat;

use App\Interfaces\CrudRepositoryInterface;

interface SeatRepositoryInterface extends CrudRepositoryInterface
{
    public function createMultiple(array $data);
}
