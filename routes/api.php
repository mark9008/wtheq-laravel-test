<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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


// Group for unauthenticated routes
Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'login_redirect'])->name('login.redirect');
    Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');
    Route::post('/signup', [RegisteredUserController::class, 'signup'])->name('register');
    Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
});

// Group for authenticated routes
Route::middleware('auth:api')->group(function () {
    Route::group(['prefix' => 'auth', 'as' => 'auth.'], function () {
        Route::get('/refresh-token', [AuthenticatedSessionController::class, 'refresh_token'])->name('refresh.token');
        Route::post('/logout', [AuthenticatedSessionController::class, 'logout'])->name('logout');
        Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.update');
    });

    Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
        Route::get('/user', [UserController::class, 'show'])->name('user');
        Route::get('/list', [UserController::class, 'index'])->name('user.list');
        Route::get('/{type}/list', [UserController::class, 'searchByType'])->name('user.list.type');
        Route::post('/update', [UserController::class, 'update'])->name('user.update');
        Route::delete('/{id}/delete', [UserController::class, 'destroy'])->name('user.delete');
    });

    // Group for product routes with custom product pricing middleware
    Route::group(['prefix' => 'products', 'as' => 'products.'], function () {
        Route::middleware('custom.product.pricing')->group(function () {
            Route::get('/list', [ProductController::class, 'index'])->name('list');
            Route::get('/{id}/get', [ProductController::class, 'show'])->name('show');
            Route::get('/search', [ProductController::class, 'searchByIds'])->name('list.ids');
        });

        Route::post('/create', [ProductController::class, 'store'])->name('create');
        Route::post('/{id}/update', [ProductController::class, 'update'])->name('update');
        Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('delete');
    });
});
