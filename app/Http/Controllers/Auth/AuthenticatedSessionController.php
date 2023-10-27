<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Responses\APIResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    protected $user;

    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        if (Auth::attempt($request->only('email', 'password'))) {
            $request->authenticate();
            $this->user = $request->user();
            $JWTToken = Auth::guard('api')->login($this->user);
            if (!auth('api')->user()->is_active)
                return APIResponse::ErrorsResponse(__function__, 'Your user is currently inactive', null, Response::HTTP_UNAUTHORIZED);
            return APIResponse::LoginResponse($JWTToken, $this->user);
        }
        return APIResponse::ErrorsResponse(__function__, 'Invalid credentials', null, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(): JsonResponse
    {
        try {
            auth('api')->logout();
            return APIResponse::LogoutResponse();
        } catch (Exception $exception) {
            return APIResponse::ErrorsResponse(__function__, $exception->getMessage(), $exception);
        }
    }

    public function login_redirect(): JsonResponse
    {
        return APIResponse::ErrorsResponse('Go to login page (/api/auth/login)', 'Unauthorized', null, Response::HTTP_UNAUTHORIZED);
    }
}
