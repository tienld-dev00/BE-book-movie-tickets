<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateUserRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [

            'password' => [
                'min:8',
                'max:20',
            ],
            'name' => [
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
                'numeric',
                'digits_between:9,11'
            ],
        ];
    }
}
