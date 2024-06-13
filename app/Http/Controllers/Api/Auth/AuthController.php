<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;
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
            return $this->responseErrors(__('auth.register_fail'));
        }

        return $this->responseSuccess([
            'user' => $result,
            'message' => __('auth.register_success'),
        ]);
    }

    /**
     * Get a JWT via given credentials.
     * @param  LoginRequest $request
     * @return HttpResponse
     */
    public function login(LoginRequest $request): Response
    {
        // Retrieve validated credentials
        $credentials = $request->validated();

        // Check if the email account exists
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            // If the user does not exist
            return $this->responseErrors(
                __('auth.user_does_not_exist'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Check if the password is correct
        if (!$token = auth()->attempt($credentials)) {
            // If the login information is invalid, return the error "auth.failed"
            return $this->responseErrors(
                __('auth.password'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Check the status of the user
        if ($user->status == 1) {
            // If the user is locked out
            return $this->responseErrors(
                __('auth.your_account_is_locked'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Check user permissions and navigate depending on role
        if ($user->role == 0) {
            // If admin, redirect to admin page or notify "admin rights" in Postman
            $responseData['message'] = 'admin rights';
            // Or redirect to admin page in frontend
        } else {
            // If user, redirect to home page or notify "user permissions" in Postman
            $responseData['message'] = 'user permissions';
            // Or redirect to home page in frontend
        }

        return $this->responseSuccess([
            'user' => $user,
            'role' => $responseData,
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


    /**
     * Retrieve the profile of the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function profile()
    {
        return $this->responseSuccess([
            'message' => '',
            'data' => auth()->user()
        ]);
    }
}
