<?php

namespace App\Repositories\Ticket;

use App\Interfaces\Ticket\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Repositories\BaseRepository;

class TicketRepository extends BaseRepository implements TicketRepositoryInterface
{
    public function __construct(Ticket $ticket)
    {
        $this->model = $ticket;
    }

    /**
     * Create multiple tickets
     * 
     * @param array $data
     * 
     * @return [type]
     */
    public function createMultiple(array $data)
    {
        return $this->model->insert($data);
    }
}
