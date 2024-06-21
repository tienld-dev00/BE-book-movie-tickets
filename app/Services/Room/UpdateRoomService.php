<?php

namespace App\Services\Room;

use App\Interfaces\Room\RoomRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class UpdateRoomService extends BaseService
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function handle()
    {
        try {
            $room = $this->roomRepository->update($this->data, $this->data['id']);

            if (isset($this->data['seats'])) {
                $seatNames = array_column($this->data['seats'], 'name');
                $room->seats()->whereNotIn('name', $seatNames)->delete();

                foreach ($this->data['seats'] as $seatData) {
                    $room->seats()->updateOrCreate([
                        'name' => $seatData['name'],
                        'room_id' => $room->id
                    ]);
                }
            }

            return $room;
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
