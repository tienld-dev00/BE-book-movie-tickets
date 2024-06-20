<?php

namespace App\Services\Movie;

use App\Enums\Paginate;
use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteMovieService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            return $this->movieRepository->delete($this->data);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
