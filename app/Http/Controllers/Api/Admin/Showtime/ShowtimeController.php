<?php

namespace App\Http\Controllers\Api\Admin\Showtime;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Showtime\CreateShowtimeRequest;
use App\Http\Requests\Api\Showtime\UpdateShowtimeRequest;
use App\Http\Resources\ShowtimeResource;
use App\Models\Showtime;
use App\Services\Showtime\CreateShowtimeService;
use App\Services\Showtime\DeleteShowtimeService;
use App\Services\Showtime\GetShowtimeByQuery;
use App\Services\Showtime\UpdateShowtimeService;
use Illuminate\Http\Request;

class ShowtimeController extends Controller
{
    /**
     * Get showtime by query
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $result = resolve(GetShowtimeByQuery::class)->setParams($query)->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.get_success'),
                'data' => ShowtimeResource::collection($result)
            ]);
        }

        return $this->responseErrors(__('messages.get_fail'));
    }

    /**
     * Create showtime
     *
     * @param  CreateShowtimeRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateShowtimeRequest $request)
    {
        $result = resolve(CreateShowtimeService::class)->setParams($request->validated())->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.create_success'),
                'data' => new ShowtimeResource($result)
            ]);
        }

        return $this->responseErrors(__('messages.create_fail'));
    }

    /**
     * Get showtime detail
     *
     * @param  Showtime  $showtime
     * @return \Illuminate\Http\Response
     */
    public function show(Showtime $showtime)
    {
        return $this->responseSuccess([
            'message' => __('messages.get_success'),
            'data' => new ShowtimeResource($showtime)
        ]);
    }

    /**
     * Update showtime
     *
     * @param  UpdateShowtimeRequest  $request
     * @param  int  $showtimeId
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateShowtimeRequest $request, $showtimeId)
    {
        $result = resolve(UpdateShowtimeService::class)->setParams([
            ...$request->validated(),
            'id' => $showtimeId
        ])->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.update_success'),
                'data' => new ShowtimeResource($result)
            ]);
        }

        return $this->responseErrors(__('messages.update_fail'));
    }

    /**
     * Delete showtime
     *
     * @param  int  $showtimeId
     * @return \Illuminate\Http\Response
     */
    public function delete($showtimeId)
    {
        $result = resolve(DeleteShowtimeService::class)->setParams(['id' => $showtimeId])->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.delete_success')
            ]);
        }

        return $this->responseErrors(__('messages.delete_fail'));
    }
}
