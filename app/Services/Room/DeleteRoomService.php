<?php

namespace App\Services\Room;

use App\Interfaces\Room\RoomRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class DeleteRoomService extends BaseService
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function handle()
    {
        try {
            $room = $this->roomRepository->find($this->data['id']);
            $room->seats()->delete();

            return $room->delete();
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
