<?php

namespace App\Http\Controllers\Api\Movie;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Movie\MovieRequest;
use App\Http\Resources\MovieCollection;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PaginationCollectionTrait;
use App\Services\Movie\ChangeStatusMovieService;
use App\Services\Movie\ClientGetMoviesService;
use App\Services\Movie\ClientShowMovieService;
use App\Services\Movie\CreateMovieService;
use App\Services\Movie\DeleteMovieService;
use App\Services\Movie\GetMoviesService;
use App\Services\Movie\GetShowingMoviesService;
use App\Services\Movie\GetUpcomingMoviesService;
use App\Services\Movie\HideMovieService;
use App\Services\Movie\ShowMovieService;
use App\Services\Movie\UpdateMovieService;
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
    public function showMovieClient($slug)
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
     * admin show movie by id 
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
            'data' => new MovieResource($result),
        ]);
    }

    /**
     * admin get list movies
     *
     * @param  int $slug
     * @return Response
     */
    public function getListMovies(Request $request)
    {
        $result = resolve(GetMoviesService::class)->setParams($request)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' =>  new MovieCollection($result),
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
        $result = resolve(CreateMovieService::class)->setParams($data)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => new MovieResource($result),
        ]);
    }

    /**
     * Update movie with id
     *
     * @param MovieRequest $request
     * @param int $movieId
     * @return Response
     */
    public function updateMovie(MovieRequest $request, $movieId)
    {
        $data['information'] = $request->validated();
        $data['id'] = $movieId;

        $result = resolve(UpdateMovieService::class)->setParams($data)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => new MovieResource($result),
        ]);
    }

    /**
     * delete Movie
     *
     * @param  int $movieId
     * @return Response
     */
    public function deleteMovie($movieId)
    {
        $result = resolve(DeleteMovieService::class)->setParams($movieId)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
        ]);
    }

    /**
     * update status Movie show/hide
     *
     * @param  int $movieId
     * @return Response
     */
    public function changeStatusMovie($movieId)
    {
        $result = resolve(ChangeStatusMovieService::class)->setParams($movieId)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
        ]);
    }


    /**
     * Get the list of currently showing movies
     *
     * @param  int $slug
     * @return Response
     */
    public function listShowingMovies(Request $request)
    {
        $result = resolve(GetShowingMoviesService::class)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' =>  $result,
        ]);
    }

    /**
     * Get the list of upcoming movies
     *
     * @param  int $slug
     * @return Response
     */
    public function listUpcomingMovies(Request $request)
    {
        $result = resolve(GetUpcomingMoviesService::class)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' =>  $result,
        ]);
    }
}
