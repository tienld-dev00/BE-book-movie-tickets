<?php

namespace App\Http\Resources;

use App\Enums\OrderStatus;
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
            'movie_id' => $this->movie->id,
            'movie' => $this->movie->name,
            'movie_image' => $this->movie->image,
            'room_id' => $this->room->id,
            'room' => $this->room->name,
            'status' => $this->status,
            'total_success_order' => $this->orders()->where('status', OrderStatus::PAYMENT_SUCCEEDED)->count(),
            'created_at' => $this->created_at,
            'updated_at' => $this->created_at,
        ];
    }
}
