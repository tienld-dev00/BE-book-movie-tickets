<?php

namespace App\Services\Room;

use App\Interfaces\Room\RoomRepositoryInterface;
use App\Services\BaseService;
use Exception;
use Illuminate\Support\Facades\Log;

class GetRoomByQueryService extends BaseService
{
    protected $roomRepository;

    public function __construct(RoomRepositoryInterface $roomRepository)
    {
        $this->roomRepository = $roomRepository;
    }

    public function handle()
    {
        try {
            return $this->roomRepository->getList($this->data)->paginate(12);
        } catch (Exception $e) {
            Log::info($e);

            return false;
        }
    }
}
