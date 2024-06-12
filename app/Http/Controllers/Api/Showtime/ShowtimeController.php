<?php

namespace App\Http\Controllers\Api\Showtime;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Showtime\FindShowTimeRequest;
use App\Http\Resources\ShowDateResource;
use App\Http\Resources\ShowtimeDetailResource;
use App\Http\Resources\ShowtimeResource;
use App\Services\Showtime\GetShowtimeService;
use App\Services\Showtime\ShowDateService;
use App\Services\Showtime\ShowtimeByDateService;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    /**
     * get list day have showtime
     *
     * @param  int $movie_id
     * @return Response
     */
    public function getShowDate($movie_id)
    {
        $result = resolve(ShowDateService::class)->setParams($movie_id)->handle();

        if (!isset($result)) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => ShowDateResource::collection($result),
        ]);
    }

    /**
     * get list showtime by day
     *
     * @param  FindShowTimeRequest $request
     * @return Response
     */
    public function getShowtimesByDate(FindShowTimeRequest $request)
    {
        $result = resolve(ShowtimeByDateService::class)->setParams($request->validated())->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' =>  ShowtimeResource::collection($result),
        ]);
    }

    /**
     * show showtime by id 
     *
     * @param  int $showtime_id
     * @return Response
     */
    public function showShowtime($showtime_id)
    {
        $result = resolve(GetShowtimeService::class)->setParams($showtime_id)->handle();

        if (!$result) {
            return $this->responseErrors(__('messages.error'));
        }

        return $this->responseSuccess([
            'message' => __('messages.success'),
            'data' => new ShowtimeDetailResource($result),
        ]);
    }
}
