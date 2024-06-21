<?php

namespace App\Services\Room;

use App\Interfaces\Room\RoomRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class CreateRoomService extends BaseService
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function handle()
    {
        try {
            $room = $this->roomRepository->create($this->data);
            $room->seats()->createMany($this->data['seats']);

            return $room;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
