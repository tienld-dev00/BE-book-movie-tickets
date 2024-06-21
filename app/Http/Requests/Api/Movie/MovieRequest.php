<?php

namespace App\Http\Requests\Api\Movie;

use Illuminate\Foundation\Http\FormRequest;

class MovieRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required',
            'release_date' => 'required|date',
            'category_id' => 'required|integer',
            'age_limit' => 'required|integer',
            'duration' => 'required|integer',
            'description' => 'required',
            'image' => 'required|image|max:5000|',
            'trailer' => 'required',
        ];
    }
}
