<?php

namespace App\Interfaces\Ticket;

use App\Interfaces\CrudRepositoryInterface;

interface TicketRepositoryInterface extends CrudRepositoryInterface
{
    public function createMultiple(array $data);
}
