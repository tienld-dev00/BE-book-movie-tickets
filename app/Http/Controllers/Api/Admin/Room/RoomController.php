<?php

namespace App\Http\Controllers\Api\Admin\Room;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Room\CreateRoomRequest;
use App\Http\Requests\Api\Room\UpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Services\Room\CreateRoomService;
use App\Services\Room\DeleteRoomService;
use App\Services\Room\GetRoomByQueryService;
use App\Services\Room\UpdateRoomService;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    /**
     * Get list room
     * 
     * @param Request $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = $request->query();
        $result = resolve(GetRoomByQueryService::class)->setParams($query)->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.get_success'),
                'data' => RoomResource::collection($result),
                'meta' => [
                    'current_page' => $result->currentPage(),
                    'from' => $result->firstItem(),
                    'last_page' => $result->lastPage(),
                    'path' => $result->path(),
                    'per_page' => $result->perPage(),
                    'to' => $result->lastItem(),
                    'total' => $result->total(),
                ],
            ]);
        }

        return $this->responseErrors(__('messages.get_fail'));
    }

    /**
     * Get room detail
     * 
     * @param Request $request
     * @param Room $room
     * 
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Room $room)
    {
        return $this->responseSuccess([
            'message' => __('messages.get_success'),
            'data' => new RoomResource($room)
        ]);
    }

    /**
     * Create room
     * 
     * @param CreateRoomRequest $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(CreateRoomRequest $request)
    {
        $result = resolve(CreateRoomService::class)->setParams($request->validated())->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.create_success'),
                'data' => new RoomResource($result)
            ]);
        }

        return $this->responseErrors(__('messages.create_fail'));
    }

    /**
     * Update room
     * 
     * @param UpdateRoomRequest $request
     * @param int $roomId
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRoomRequest $request, int $roomId)
    {
        $result = resolve(UpdateRoomService::class)->setParams([
            ...$request->validated(),
            'id' => $roomId
        ])->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.update_success'),
                'data' => new RoomResource($result)
            ]);
        }

        return $this->responseErrors(__('messages.update_fail'));
    }

    /**
     * Delete room
     * 
     * @param int $roomId
     * 
     * @return \Illuminate\Http\Response
     */
    public function delete(int $roomId)
    {
        $result = resolve(DeleteRoomService::class)->setParams(['id' => $roomId])->handle();

        if ($result) {
            return $this->responseSuccess([
                'message' => __('messages.delete_success')
            ]);
        }

        return $this->responseErrors(__('messages.delete_fail'));
    }
}
