<?php

namespace App\Services\Movie;

use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Models\Movie;
use App\Services\BaseService;
use Aws\S3\Exception\S3Exception;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CreateMovieService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            if (file_exists($this->data['image'])) {
                $file = $this->data['image'];
                $time = strtotime(date('Y-m-d H:i:s'));
                $fileName = $file->getClientOriginalName() . '_' . $time;

                $path = Storage::put(
                    'images/movie',
                    $file,
                    $fileName,
                    ['visibility' => 'public']
                );

                $url = Storage::url($path);
                $this->data['image'] = $url;

                return $this->movieRepository->create($this->data);
            }
        } catch (S3Exception $e) {
            Log::error('S3 error: ' . $e->getAwsErrorMessage());

            return false;
        } catch (Exception $e) {
            Log::error('An error occurred: ' . $e->getMessage());

            return false;
        }
    }
}
