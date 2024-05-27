<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class BaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Handle a failed validation attempt
     *
     * @param mixed $validator
     * @return void
     *
     * @throws ValidationException
     */
    public function failedValidation(Validator $validator): void
    {
        $messages = $validator->errors()->toArray();

        foreach ($messages as $key => $item) {
            $messages[$key] = $item[0];
        }

        $statusCodeError = Response::HTTP_UNPROCESSABLE_ENTITY;
        $json = [
            'code' => $statusCodeError,
            'errors' => $messages,
        ];
        $response = new JsonResponse($json, $statusCodeError);
        throw (new ValidationException($validator, $response))->status($statusCodeError);
    }
}
