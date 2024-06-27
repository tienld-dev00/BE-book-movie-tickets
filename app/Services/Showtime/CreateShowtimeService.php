<?php

namespace App\Services\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateShowtimeService extends BaseService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    public function handle()
    {
        try {
            return $this->showtimeRepository->create($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
