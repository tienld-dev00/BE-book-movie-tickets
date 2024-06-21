<?php

namespace App\Repositories\Room;

use App\Interfaces\Room\RoomRepositoryInterface;
use App\Models\Room;
use App\Repositories\BaseRepository;

class RoomRepository extends BaseRepository implements RoomRepositoryInterface
{
    public function __construct(Room $room)
    {
        $this->model = $room;
    }

    /**
     * Get list room
     * 
     * @param array $data
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getList(array $data)
    {
        $builder = $this->model->query();

        if (isset($data['keyword'])) {
            $builder->where(function ($query) use ($data) {
                $query->orWhere('name', 'like', '%' . $data['keyword'] . '%');
            });
        }

        $builder->orderBy('created_at', 'desc');

        return $builder;
    }
}
