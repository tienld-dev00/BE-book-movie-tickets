<?php

namespace App\Http\Requests\Api\Payment;

use App\Http\Requests\Api\BaseRequest;
use App\Rules\SeatInRoomRule;
use App\Rules\SeatNotBookedRule;
use App\Rules\SeatsSelectedInFirebaseRule;
use App\Rules\ValidShowtimeRule;

class ProcessPaymentRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $showtime_id = $this->input('showtime_id');

        return [
            'order_id' => [
                'required',
                'unique:orders,id'
            ],
            'showtime_id' => [
                'required',
                'exists:showtimes,id',
                new ValidShowtimeRule()
            ],
            'seats' => [
                'required',
                'array',
                new SeatsSelectedInFirebaseRule($showtime_id)
            ],
            'seats.*' => [
                'required',
                'exists:seats,id',
                new SeatNotBookedRule($showtime_id),
                new SeatInRoomRule($showtime_id)
            ]
        ];
    }
}
