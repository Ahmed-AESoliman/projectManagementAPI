<?php

use App\Http\Resources\AuthenticatedUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\UsersController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
Route::post('resend-verification', [EmailVerificationController::class, 'resend'])->name('verification.resend');
Route::post('/forgot-password', [AuthController::class, 'forgetPassword'])->middleware('guest')->name('password.email');

Route::get('/reset-password/{token}/{email}', function ($token, $email) {
    return response()->json(['token' => $token, 'email' => $email]);
})->middleware('guest')->name('password.reset');


Route::post('/reset-password', [AuthController::class, 'restPassword'])->middleware('guest')->name('password.update');

// route::post('/create-password', function (Request $request) {
//     $request->validate([
//         'token' => 'required',
//         'password' => 'required|min:8|confirmed',
//     ]);
//     $tokenData = DB::table('password_resets')
//         ->where('token', $request->token)->first();
//     if (!$tokenData) {
//         return  response()->json([
//             'error' => 'invalid token!'
//         ]);
//     }
//     $user = User::where('email', $tokenData->email)->firstOrFail();
//     $newPassword = Hash::make($request->password);
//     $user->update([
//         'password' => $newPassword,
//         'active' => true
//     ]);
//     return response()->json(['message' => 'password saved successfully'], 200);
// });

Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::post('/register', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth:api', 'verified'])->group(function () {
    Route::get('/user', function (Request $request) {
        return new  AuthenticatedUserResource($request->user());
    });
    route::post('/logout', [AuthController::class, 'loggout']);

    //users companys
    route::post('/create-user', [UsersController::class, 'store']);
    route::put('/{id}/edit-user', [UsersController::class, 'update']);
});
