<?php

use App\Http\Resources\AuthenticatedUserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\OrderCategoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\WorkerController;
use App\Models\OrderCategory;
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

    //project routes
    route::post('/projects/create-project', [ProjectController::class, 'store']);
    route::put('/projects/{id}/edit-project', [ProjectController::class, 'update']);

    //employee routes
    route::get('/employees', [EmployeeController::class, 'index']);
    route::post('/employees/create-employee', [EmployeeController::class, 'store']);
    route::put('/employees/{id}/edit-employee', [EmployeeController::class, 'update']);
    route::delete('/employees/{id}/delete-employee', [EmployeeController::class, 'destroy']);

    //supplier routes
    route::get('/suppliers', [SupplierController::class, 'index']);
    route::post('/suppliers/create-supplier', [SupplierController::class, 'store']);
    route::put('/suppliers/{id}/edit-supplier', [SupplierController::class, 'update']);
    route::delete('/suppliers/{id}/delete-supplier', [SupplierController::class, 'destroy']);

    //worker routes
    route::get('/workers', [WorkerController::class, 'index']);
    route::post('/workers/create-worker', [WorkerController::class, 'store']);
    route::put('/workers/{id}/edit-worker', [WorkerController::class, 'update']);
    route::delete('/workers/{id}/delete-worker', [WorkerController::class, 'destroy']);
    // category routes
    route::get('/categories', [OrderCategoryController::class, 'index']);
    route::get('/categories/sub-categories', [OrderCategoryController::class, 'subcategory']);
    route::post('/categories/create-category', [OrderCategoryController::class, 'store']);
    route::post('/categories/create-sub-categories', [OrderCategoryController::class, 'storeSubcategory']);
    route::put('/categories/{id}/edit-category', [OrderCategoryController::class, 'update']);
    route::delete('/categories/{id}/delete-category', [OrderCategoryController::class, 'destroy']);
});
