<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\EmailAuthenticationTime;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\UpdateUserRequest;
use App\Models\User;
use App\Services\Auth\RegisterUserService;
use Exception;
use Illuminate\Http\Response as HttpResponse;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use App\Mail\Auth\VerifyMailRegister;

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
     *
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

        // Check if the user is logged in via Google
        if ($user->google_id) {
            // If the user is logged in via Google and password is not null, allow login
            if ($user->password !== null) {
                // Check if the password is correct
                if (!$token = auth()->attempt($credentials)) {
                    // If the login information is invalid
                    return $this->responseErrors(
                        __('auth.password'),
                        Response::HTTP_UNAUTHORIZED
                    );
                }
            } else {
                // If the password is null, return error
                return $this->responseErrors(
                    __('auth.email_linked_to_google'),
                    Response::HTTP_UNAUTHORIZED
                );
            }
        } else {
            // Check if the password is correct for non-Google login attempts
            if (!$token = auth()->attempt($credentials)) {
                // If the login information is invalid
                return $this->responseErrors(
                    __('auth.password'),
                    Response::HTTP_UNAUTHORIZED
                );
            }
        }

        // Check if the password is correct
        if (!$token = auth()->attempt($credentials)) {
            // If the login information is invalid, return the error "auth.failed"
            return $this->responseErrors(
                __('auth.password'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // If the user is logged in via Google, skip email verification check
        if (!$user->google_id) {
            // Check email verification based on user role
            if ($user->role == UserRole::USER && is_null($user->email_verified_at)) {
                // Check if the previous verification link has expired
                $expiryTime = now()->subMinutes(EmailAuthenticationTime::TIME);
                if ($user->created_at->lessThan($expiryTime)) {
                    // If the verification link has expired, send a new verification email
                    $verificationUrl = URL::temporarySignedRoute(
                        'verify_email',
                        now()->addMinutes(EmailAuthenticationTime::TIME),
                        ['id' => $user->id]
                    );

                    Mail::to($user->email)->send(new VerifyMailRegister($user, $verificationUrl));

                    return $this->responseErrors(
                        __('auth.verification_email_resent'),
                        Response::HTTP_UNAUTHORIZED
                    );
                } else {
                    // If the verification link is still valid, notify the user to check their email
                    return $this->responseErrors(
                        __('auth.email_not_verified_check_email'),
                        Response::HTTP_UNAUTHORIZED
                    );
                }
            }
        }

        // Check the status of the user
        if ($user->status == UserStatus::LOCK) {
            // If the user is locked out
            return $this->responseErrors(
                __('auth.your_account_is_locked'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Check user permissions and navigate depending on role
        if ($user->role == UserStatus::ACTIVE) {
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

    /**
     * Update the specified user in storage.
     *
     * @param  \App\Http\Requests\UpdateUserRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();
        $update = $request->validated();

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $extention = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extention;
            $file->move('avatars', $filename);
            $update['avatar'] = $filename;
        }

        $user->update($update);

        return response()->json([
            'message' => __('users.update_success'),
            'data' => auth()->user(),
        ]);
    }

    /**
     * Change the user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $user = Auth::user();

        // If user has a password, check the old password
        if (!is_null($user->password) && !Hash::check($request->old_password, $user->password)) {
            return $this->responseErrors([
                'message' => __('auth.old_password_fail')
            ], 400);
        }

        // Update the user's password
        $user->password = $request->new_password;
        $user->update();

        return $this->responseSuccess([
            'message' => __('auth.password_changed_success')
        ], 200);
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
            Log::error('logout fail', ['result' => $e->getMessage()]);

            return $this->responseErrors('has an error when register user');
        }
    }

    /**
     * verify email registration.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verifyEmail(Request $request)
    {
        Log::info($request);
        try {
            $user = User::find($request->id);

            if (!$user) {
                return $this->responseErrors('User not found');
            }

            // Check if email has been verified
            if ($user->email_verified_at !== null) {
                // return redirect()->to('http://localhost:8080/confirmed-account')->with('message', 'Email has been verified');
                return $this->responseSuccess([
                    'message' => 'Email has been verified',
                ], 200);
            }

            // Get the expiration time from the request
            $expires = $request->query('expires');

            // Check if the verification link has expired
            if (now()->timestamp > $expires) {
                return $this->responseErrors('Email verification link has expired');
            }

            $user->email_verified_at = now();
            $user->update();

            // return redirect()->to('http://localhost:8080/confirmed-account')->with('message', 'Email successfully verified');
            return $this->responseSuccess([
                'message' => 'Email successfully verified',
            ], 200);
        } catch (Exception $e) {
            Log::error("Email verification failed", ['result' => $e->getMessage()]);

            return $this->responseErrors('Has an error when verifying email');
        }
    }
}
