<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Services\Auth\RegisterUserService;
use Exception;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        $result = resolve(RegisterUserService::class)->setParams($request->validated())->handle();

        if (!$result) {
            return $this->responseErrors('has an error when register user');
        }

        return $this->responseSuccess([
            'user' => $result,
            'message' => 'register success',
        ]);
    }

    /**
     * Get a JWT via given credentials.
     * @param  LoginRequest $request
     * @return HttpResponse
     */
    public function login(LoginRequest $request): Response
    {
        $credentials = $request->validated();
        if (!$token = auth()->attempt($credentials)) {
            return $this->responseErrors('Unauthorized', Response::HTTP_UNAUTHORIZED);
        }

        return $this->responseSuccess([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        try {
            auth()->logout();

            return response()->json(['message' => 'User successfully signed out']);
        } catch (Exception $e) {
            Log::error("logout fail", ['result' => $e->getMessage()]);

            return $this->responseErrors('has an error when register user');
        }
    }

    /**
     * verify email register.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail()
    {
        try {
            auth()->logout();

            return response()->json(['message' => 'User successfully signed out']);
        } catch (Exception $e) {
            Log::error("logout fail", ['result' => $e->getMessage()]);

            return $this->responseErrors('has an error when register user');
        }
    }
}
