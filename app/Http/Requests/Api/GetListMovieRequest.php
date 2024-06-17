<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GetListMovieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'key_word' => 'string',
            'per_page' => 'integer',
            'filter' => 'integer',
            'sort' => 'integer',
        ];
    }
}
