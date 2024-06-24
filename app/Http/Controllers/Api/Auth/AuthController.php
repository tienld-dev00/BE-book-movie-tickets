<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\EmailAuthenticationTime;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\ChangePasswordRequest;
use App\Http\Requests\Api\Auth\checkForgotPasswordRequest;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\ResetPasswordRequest;
use App\Http\Requests\Api\Auth\UpdateUserRequest;
use App\Mail\Auth\ForgotPassword;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
                return $this->responseErrors(
                    __('auth.email_not_verified'),
                    Response::HTTP_CONFLICT
                );
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
        try {
            $user = User::find($request->id);

            if (!$user) {
                return $this->responseErrors([
                    'success' => false,
                    'message' => 'User not found'
                ], 404);
            }

            // Check if email has been verified
            if ($user->email_verified_at !== null) {
                return $this->responseSuccess([
                    'success' => true,
                    'message' => 'Email has been verified',
                ]);
            }

            // Get the expiration time from the request
            $expires = $request->query('expires');

            // Check if the verification link has expired
            if (now()->timestamp > $expires) {
                return $this->responseErrors([
                    'success' => false,
                    'message' => 'Email verification link has expired'
                ]);
            }

            $user->email_verified_at = now();
            $user->update();

            return $this->responseSuccess([
                'success' => true,
                'message' => 'Email successfully verified',
            ], 200);
        } catch (Exception $e) {
            Log::error("Email verification failed", ['result' => $e->getMessage()]);

            return $this->responseErrors([
                'success' => false,
                'message' => 'An error occurred when verifying email'
            ], 500);
        }
    }

    public function resendActivationEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->email_verified_at) {
            return response()->json(['message' => 'Email already verified'], 400);
        }

        $urlVerify = 'http://localhost:3000/confirmed-account?expired=' . now()->addMinutes(EmailAuthenticationTime::TIME)->timestamp . '&user_id=' . $user->id;

        Mail::to($user->email)->send(new VerifyMailRegister($user, $urlVerify));

        return response()->json(['message' => 'Activation email sent successfully']);
    }

    /**
     * Send password change code.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function checkForgotPassword(checkForgotPasswordRequest $request)
    {
        // Retrieve validated email
        $validatedData = $request->validated();

        // Check if the email account exists
        $user = User::where('email', $validatedData['email'])->first();

        if (!$user) {
            // If the user does not exist
            return $this->responseErrors(
                __('auth.user_does_not_exist'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Lấy thông tin reset password của user từ bảng password_resets
        $passwordReset = DB::table('password_resets')->where('email', $user->email)->first();

        // Kiểm tra logic về số lần gửi mã và thời gian
        if ($passwordReset) {
            $createdAt = \Carbon\Carbon::parse($passwordReset->created_at);
            $currentTime = \Carbon\Carbon::now();
            $diffInMinutes = $currentTime->diffInMinutes($createdAt);

            // Nếu số lần gửi mã nhiều hơn 3 và thời gian chưa quá 60 phút
            if ($passwordReset->count >= 3 && $diffInMinutes < EmailAuthenticationTime::TIME) {
                return $this->responseErrors(
                    __('auth.password_reset_limit_exceeded'),
                    Response::HTTP_BAD_REQUEST
                );
            }

            // Nếu thời gian đã quá 60 phút, reset lại bộ đếm
            if ($diffInMinutes >= EmailAuthenticationTime::TIME) {
                $count = 1; // reset count về 1 vì sẽ gửi mã mới
            } else {
                $count = $passwordReset->count + 1; // tăng count
            }
        } else {
            // Nếu không có bản ghi nào, khởi tạo count là 1
            $count = 1;
        }

        // Generate a 6-digit verification code (or token)
        $verificationCode = mt_rand(100000, 999999);

        // Store the verification code and creation time in password_resets table
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => $verificationCode,
                'created_at' => now(),
                'count' => $count
            ]
        );

        // Send the verification code via email
        Mail::to($user->email)->send(new ForgotPassword($user, $verificationCode));

        return response()->json(['message' => __('auth.password_change_code')]);
    }


    /**
     * reset password.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {

        $request->validated();

        // Retrieve password reset record by verification code
        $resetRecord = DB::table('password_resets')
            ->where('token', $request->verification_code)
            ->first();

        if (!$resetRecord || Carbon::parse($resetRecord->created_at)->addMinutes(EmailAuthenticationTime::TIME)->isPast()) {
            return $this->responseErrors(
                __('auth.invalid_or_expired_code'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        // Update user's password
        $user = User::where('email', $resetRecord->email)->first();
        if (!$user) {
            return $this->responseErrors(
                __('auth.user_does_not_exist'),
                Response::HTTP_UNAUTHORIZED
            );
        }

        $user->password = $request->password;
        $user->save();

        // Delete the password reset record after successful password change
        DB::table('password_resets')->where('email', $resetRecord->email)->delete();

        return response()->json(['message' => __('auth.password_changed_success')]);
    }
}
