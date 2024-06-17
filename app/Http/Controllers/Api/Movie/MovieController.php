<?php

namespace App\Http\Controllers\Api\Movie;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\GetListMovieRequest;
use App\Http\Requests\Api\Movie\MovieRequest;
use App\Services\Movie\AddMovieService;
use App\Services\Movie\GetMoviesService;
use App\Services\Movie\ShowMovieService;
use Illuminate\Http\Request as HttpRequest;

class MovieController extends Controller
{
    /**
     * show movie by id 
     *
     * @param  int $slug
     * @return Response
     */
    public function showMovie($slug)
    {
        $result = resolve(ShowMovieService::class)->setParams($slug)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => $result,
        ]);
    }

    /**
     * get list movies
     *
     * @param  int $slug
     * @return Response
     */
    public function getListMovies(GetListMovieRequest $request)
    {
        $data = $request->validated();
        $result = resolve(GetMoviesService::class)->getListMovies($data)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => $result,
        ]);
    }

    /**
     * create new movie
     *
     * @param  MovieRequest $request
     * @return Response
     */
    public function addMovie(MovieRequest $request)
    {
        $data = $request->validated();
        $result = resolve(AddMovieService::class)->setParams($data)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => $result,
        ]);
    }
}
