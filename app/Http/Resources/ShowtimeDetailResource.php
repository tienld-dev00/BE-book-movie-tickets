<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeDetailResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'price' => $this->price,
            'movie' => $this->movie,
            'room' => new RoomResource($this->room),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
