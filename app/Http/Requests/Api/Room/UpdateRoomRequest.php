<?php

namespace App\Http\Requests\Api\Room;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoomRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'string'
            ],
            'row_number' => [
                'required',
                'integer'
            ],
            'column_number' => [
                'required',
                'integer'
            ],
            'seats' => [
                'required',
                'array'
            ],
            'seats.*.name' => [
                'required',
                'string'
            ]
        ];
    }
}
