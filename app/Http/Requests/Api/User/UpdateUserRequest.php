<?php

namespace App\Http\Requests\Api\User;

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
                // 'max:2048', // Giới hạn dung lượng ảnh
            ],
            'role' => [
                'integer'
            ],
            'status' => [
                'integer'
            ],
        ];
    }
}
