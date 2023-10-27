<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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

Route::group(['guard' => 'api'],
    function () {
        Route::group(['prefix' => 'auth'],
            function () {
                Route::get('/login', [AuthenticatedSessionController::class, 'login_redirect'])->name('login.redirect');
                Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');
                Route::post('/signup', [RegisteredUserController::class, 'signup'])->name('register');
                Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
            });
    });
Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'auth'],
        function () {
            Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
            Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
        });

    Route::group(['prefix' => 'profile'],
        function () {
            Route::get('/user', [UserController::class, 'show'])->name('user');
            Route::get('/list', [UserController::class, 'index'])->name('user.list');
            Route::post('/update', [UserController::class, 'update'])->name('user.update');
            Route::delete('/delete', [UserController::class, 'destroy'])->name('user.delete');
        });

    Route::group(['prefix' => 'products']
        , function () {
            Route::get('/list', [ProductController::class, 'index'])->middleware('custom.product.pricing')->name('product.list');
            Route::get('/{id}/get', [ProductController::class, 'show'])->middleware('custom.product.pricing')->name('product.show');
            Route::post('/create', [ProductController::class, 'store'])->name('product.create');
            Route::post('/{id}/update', [ProductController::class, 'update'])->name('product.update');
            Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('product.delete');
        });
});
