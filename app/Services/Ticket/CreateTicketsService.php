<?php

namespace App\Services\Ticket;

use App\Interfaces\Ticket\TicketRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateTicketsService extends BaseService
{
    protected $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function handle()
    {
        try {
            return $this->ticketRepository->createMultiple($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
