<?php

namespace App\Services\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class ShowtimeByDateService extends BaseService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    public function handle()
    {
        try {
            $showtimes =  $this->showtimeRepository->getShowtimeByDate($this->data);

            return $showtimes;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
