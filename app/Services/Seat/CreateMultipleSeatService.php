<?php

namespace App\Services\Seat;

use App\Interfaces\Seat\SeatRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateMultipleSeatService extends BaseService
{
    protected $seatRepository;

    public function __construct(SeatRepositoryInterface $seatRepository)
    {
        $this->seatRepository = $seatRepository;
    }

    public function handle()
    {
        try {
            return $this->seatRepository->createMultiple($this->data['seats']);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
