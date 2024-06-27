<?php

namespace App\Services\Showtime;

use App\Interfaces\Showtime\ShowtimeRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetShowtimeByQuery extends BaseService
{
    protected $showtimeRepository;

    public function __construct(ShowtimeRepositoryInterface $showtimeRepository)
    {
        $this->showtimeRepository = $showtimeRepository;
    }

    public function handle()
    {
        try {
            return $this->showtimeRepository->getShowtimeByQuery($this->data)->get();
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
