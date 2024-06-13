<?php

namespace App\Http\Controllers\Api\Movie;

use App\Http\Controllers\Controller;
use App\Services\Movie\GetMovieService;

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
        $result = resolve(GetMovieService::class)->setParams($slug)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => $result,
        ]);
    }
}
