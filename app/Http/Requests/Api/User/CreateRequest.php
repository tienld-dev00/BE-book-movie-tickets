<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;

class CreateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'email' => [
                'required',
                'email',
                'unique:users,email',
            ],
            'password' => [
                'required',
                'min:8',
                'max:20',
            ],
            'name' => [
                'required',
                'string',
                'between:6,255'
            ],
            'avatar' => [

            ],
            'role' => [
                'integer'
            ],
            'status' => [
                'integer'
            ],
            'google_id' => [

            ],
            'facebook_id' => [

            ],
            'phone_number' => [
                'required',
                'numeric',
                'digits_between:9,11'
            ],
        ];
    }
}
