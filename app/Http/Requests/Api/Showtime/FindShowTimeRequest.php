<?php

namespace App\Http\Requests\Api\Showtime;

use Illuminate\Foundation\Http\FormRequest;

class FindShowTimeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'movie_id' => 'required|integer',
            'date' => 'required|date'
        ];
    }
}
