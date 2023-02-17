<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\AuthenticatedUserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|max:225',
            'email' => 'required|unique:users|email',
            'password' => 'required|min:6'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
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
}
