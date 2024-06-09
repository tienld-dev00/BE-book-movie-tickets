<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShowtimeResource extends JsonResource
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
            'movie' => $this->movie->name,
            'room' => $this->room->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
            // 'seats' => $this->room->seat ?
            //     SeatResource::collection($this->room->seat)
            //     : [],
        ];
    }
}