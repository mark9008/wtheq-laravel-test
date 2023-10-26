<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;

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

Route::middleware(['auth:api'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['guard' => 'api'],
    function () {
        Route::group(['prefix' => 'auth'],
            function () {
                Route::get('/login', [AuthenticatedSessionController::class, 'login_redirect'])->name('login.redirect');
                Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');
                Route::post('/signup', [RegisteredUserController::class, 'signup'])->name('register');
                Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
                Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
            });
    });
Route::middleware('auth:api')->get('/auth/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
