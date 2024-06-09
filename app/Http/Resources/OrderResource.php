<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id' => $this->id,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'user_id' => $this->user_id,
            'showtime_id' => $this->showtime_id,
            'showtime' => new ShowtimeResource($this->showtime),
            'amount' => $this->getAmount(),
            'tickets' => TicketResource::collection($this->tickets),
            'transaction_id' => isset($this->payments[0]->payment_intent_id) ? $this->payments[0]->payment_intent_id : '',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];

        $data['user'] = $this->user;

        return $data;
    }
}
