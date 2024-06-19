<?php

namespace App\Services\Movie;

use App\Enums\MovieStatus;
use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Services\BaseService;
use Aws\S3\Exception\S3Exception;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class HideMovieService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            $updateData = ['status' => MovieStatus::HIDE];

            $updatedMovie = $this->movieRepository->update($updateData, $this->data);

            return $updatedMovie;
        } catch (Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());

            return false;
        }
    }
}
