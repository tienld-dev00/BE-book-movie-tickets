<?php

namespace App\Services\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class GetShowtimeService extends BaseService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    public function handle()
    {
        try {
            $showtime =  $this->showtimeRepository->getShowtime($this->data);

            return $showtime;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
