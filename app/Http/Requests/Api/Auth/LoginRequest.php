<?php

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\BaseRequest;

class LoginRequest extends BaseRequest
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
                'exists:users,email',
            ],
            'password' => [
                'required',
                'min:8',
                'max:20',
                // 'regex:/\s\s+/',
            ],
        ];
    }
}
