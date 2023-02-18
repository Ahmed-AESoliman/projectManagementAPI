<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function response;

class EmailVerificationController extends Controller
{
    public function verify(Request $request)
    {
        $user = User::findOrFail($request->id);

        if ($user->email_verified_at) {
            return '';
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }
        // $user->removeRole('user');
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => Str::random(60),
            'created_at' => Carbon::now()
        ]);
        $tokenData = DB::table('password_resets')
            ->where('email', $user->email)
            ->first();
        $token = $tokenData->token;
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Account Activated Successfully !',
                'CreatePasswordToken' => $token
            ], 200);
        }
    }
    public function resend(Request $request)
    {
        $user = User::whereEmail($request->email)->first();
        $user->sendEmailVerificationNotification();
        return response()->json(['Email was sent Successfully !'], 200);
    }
}
