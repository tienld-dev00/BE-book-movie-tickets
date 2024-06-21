<?php

namespace App\Interfaces\Room;

use App\Interfaces\CrudRepositoryInterface;

interface RoomRepositoryInterface extends CrudRepositoryInterface
{
    public function getList(array $data);
}
