<?php

namespace App\Http\Requests\Api\Showtime;

use App\Enums\ShowtimeStatus;
use App\Http\Requests\Api\BaseRequest;
use App\Rules\ShowtimeOverlapRule;
use Illuminate\Validation\Rule;

class CreateShowtimeRequest extends BaseRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'movie_id' => [
                'required',
                'exists:movies,id'
            ],
            'room_id' => [
                'required',
                'exists:rooms,id'
            ],
            'end_time' => [
                'required',
                'after:start_time'
            ],
            'start_time' => [
                'required',
                'before:end_time',
                'after:now',
                new ShowtimeOverlapRule($this->input('start_time'), $this->input('end_time'), $this->input('room_id'))
            ],
            'price' => [
                'required',
                'min:1'
            ],
            'status' => [
                'required',
                Rule::in(ShowtimeStatus::getValues()),
            ]
        ];
    }
}
