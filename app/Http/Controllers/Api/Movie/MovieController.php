<?php

namespace App\Http\Controllers\Api\Movie;

use App\Http\Controllers\Controller;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationCollectionTrait;
use App\Services\Movie\ClientGetMoviesService;
use App\Services\Movie\ClientShowMovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    use PaginationCollectionTrait;
    /**
     * show movie by id 
     *
     * @param  int $slug
     * @return Response
     */
    public function showMovie($slug)
    {
        $result = resolve(ClientShowMovieService::class)->setParams($slug)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => new MovieResource($result),
        ]);
    }

    /**
     * get list movies
     *
     * @param  int $slug
     * @return Response
     */
    public function getListMovies(Request $request)
    {
        $result = resolve(ClientGetMoviesService::class)->setParams($request)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' =>  new MovieCollection($result),
        ]);
    }
}
