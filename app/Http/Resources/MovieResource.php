<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data =  [
            'id' => $this->id,
            'name' => $this->name,
            'release_date' => $this->release_date,
            'category' => $this->category->name,
            'age_limit' => $this->age_limit,
            'duration' => $this->duration,
            'description' => $this->description,
            'image' => $this->image,
            'trailer' => $this->trailer,
            'slug' => $this->slug,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($this->total_orders !== null) {
            $data['total_orders'] = $this->total_orders;
        }

        return $data;
    }
}
