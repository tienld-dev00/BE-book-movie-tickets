<?php

namespace App\Services\Movie;

use App\Enums\Paginate;
use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class ClientGetMoviesService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            $data = [
                'key_word' => strip_tags($this->data['key_word']) ?? null,
                'per_page' => $this->data['per_page'] ?? Paginate::DEFAULT,
                'sort_field' => $this->data['sort_field'] ?? 'created_at',
                'sort_direction' => $this->data['sort_direction'] ?? 'DESC'
            ];
            $movie =  $this->movieRepository->getListMoviesClient($data);

            return $movie;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
