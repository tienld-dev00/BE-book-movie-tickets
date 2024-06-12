<?php

namespace App\Services\Movie;

use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetMovieService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            $movie =  $this->movieRepository->getMovie($this->data);

            return $movie;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
