<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthenticatedUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'full_name' => 'required|max:225',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'full_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $user->notify(new VerifyEmailNotification());
        return new AuthenticatedUserResource($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email|string',
            'password' => [
                'required',
            ],
            'remember_token' => 'boolean',

        ]);

        $remember = $credentials["remeber"] ?? false;
        unset($credentials["remember_token"]);


        if (!Auth::attempt($credentials, $remember)) {
            return response([
                'error' => [
                    'email' => ['The provided credentials are not correct']
                ]
            ], 422);
        }
        $user = Auth::user();
        if (!$user->hasVerifiedEmail()) {
            return response([
                'email' => 'Your email address is not verified.'
            ], 403);
        }
        return new AuthenticatedUserResource($user);
    }
    public function loggout(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory
    {
        $user = Auth::user();
        $user->token()->delete();

        return response([
            'success' => true
        ]);
    }

    public function forgetPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->exists();
        if ($user) {
            $status = Password::sendResetLink(
                $request->only('email')
            );
            return $status === Password::RESET_LINK_SENT
                ? response()->json(['message' => 'email was sent !'], 200)
                :  response()->json(['error' => $status], 400);
        }
        return response()->json(['message' => 'email not found !'], 400);
    }

    public function restPassword(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ?  response()->json(['message' => 'password saved successfully'], 200)
            :  response()->json(['message' => 'somthing wrong'], 400);
    }
}
