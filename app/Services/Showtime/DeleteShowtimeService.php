<?php

namespace App\Services\Showtime;

use App\Enums\OrderStatus;
use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteShowtimeService extends BaseService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    public function handle()
    {
        try {
            $showtime = $this->showtimeRepository->find($this->data['id']);
            $totalSuccessOrder = $showtime->orders()->where('status', OrderStatus::PAYMENT_SUCCEEDED)->count();

            if ($totalSuccessOrder === 0) {
                return $this->showtimeRepository->delete($this->data['id']);
            }

            return false;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
