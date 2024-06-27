<?php

namespace App\Services\Movie;

use App\Interfaces\Movie\MovieRepositoryInterface;
use App\Services\BaseService;
use Aws\S3\Exception\S3Exception;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UpdateMovieService extends BaseService
{
    protected $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function handle()
    {
        try {
            $thisMovie = $this->movieRepository->find($this->data['id']);

            if ($thisMovie) {
                $oldUrl = $thisMovie->image;
                if (isset($this->data['information']['image'])) {
                    if (file_exists($this->data['information']['image'])) {
                        $file = $this->data['information']['image'];
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $directory = 'images/movie';

                        $path = Storage::put(
                            $directory,
                            $file,
                            $fileName,
                            ['visibility' => 'public']
                        );

                        $url = Storage::url($path);
                        $this->data['information']['image'] = $url;

                        $updatedMovie = $this->movieRepository->update($this->data['information'], $this->data['id']);

                        if ($updatedMovie) {
                            $baseUrl = urldecode(parse_url($oldUrl)['path']);

                            if (Storage::exists($baseUrl)) {
                                Storage::delete($baseUrl);
                            }
                        }
                    }
                } else {
                    $this->data['information']['image'] = $oldUrl;

                    $updatedMovie = $this->movieRepository->update($this->data['information'], $this->data['id']);
                }

                return $updatedMovie;
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
