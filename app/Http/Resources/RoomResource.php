<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'name' => $this->name,
            'row_number' => $this->row_number,
            'column_number' => $this->column_number,
        ];

        if ($request->route()->getActionMethod() !== 'index') {
            $data['seats'] = SeatResource::collection($this->seat->sortBy('name'));
        }

        return $data;
    }
}
