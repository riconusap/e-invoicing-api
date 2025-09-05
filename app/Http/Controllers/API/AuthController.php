<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str as StrHelper;

class AuthController extends Controller
{
    public function __construct()
    {
        // Middleware 'auth:api' akan melindungi method kecuali 'login' dan 'register'
        $this->middleware('auth:api', ['except' => ['login', 'register', 'isLoggedIn']]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('email', 'password');
        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Wrong Email or Password'], 401);
        }

        // Update last login information
        $user = auth('api')->user();
        $user->updateLastLogin($request->ip());

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'uuid' => Str::uuid(),
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }

    public function me()
    {
        return response()->json(auth('api')->user());
    }

    public function isLoggedIn()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
                'is_logged_in' => false
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'User is logged in',
            'is_logged_in' => true,
            'user' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
            ]
        ]);
    }

    public function loginInfo()
    {
        $user = auth('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'uuid' => $user->uuid,
                'name' => $user->name,
                'email' => $user->email,
                'last_login_at' => $user->last_login_at,
                'last_login_ip' => $user->last_login_ip,
                'current_ip' => request()->ip(),
                'login_duration' => $user->last_login_at ? now()->diffInMinutes($user->last_login_at) : null,
            ]
        ]);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            return $this->createNewToken($token);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Token could not be refreshed'], 401);
        }
    }

    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Email not found'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $token = Password::createToken($user);

        try {
            $user->sendPasswordResetNotification($token);
            return response()->json(['message' => 'Reset link sent to your email']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unable to send reset link: ' . $e->getMessage()], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(StrHelper::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password reset successfully']);
        } else {
            return response()->json(['error' => 'Unable to reset password'], 400);
        }
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60, // Waktu dalam detik
            'user' => auth('api')->user()
        ]);
    }
}
