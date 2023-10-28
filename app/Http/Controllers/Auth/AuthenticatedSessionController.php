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
    /**
     * Handle an incoming authentication request.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        // Attempt to authenticate the user based on provided email and password
        if (Auth::attempt($request->only('email', 'password'))) {
            // Authentication was successful, proceed to the next steps
            $request->authenticate();

            // get the authenticated user
            $user = $request->user();

            // check if the user is active
            if (!$user->is_active)
                // The user is not active, return an error response with HTTP Status Code 401 (Unauthorized)
                return APIResponse::ErrorsResponse(__function__, 'Your user is currently inactive', null, Response::HTTP_UNAUTHORIZED);

            // The user is active, generate a JWT Token
            $JWTToken = auth('api')->login($user);

            // return a success response with HTTP Status Code 200 (OK)
            return APIResponse::LoginResponse($JWTToken, $user);
        }
        // Authentication failed, return an error response with HTTP Status Code 401 (Unauthorized)
        return APIResponse::ErrorsResponse(__function__, 'Invalid credentials', null, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Destroy an authenticated session.
     */
    public function logout(): JsonResponse
    {
        try {
            // Attempt to log the user out from the 'api' guard
            auth('api')->logout();

            // return a success response with HTTP Status Code 200 (OK)
            return APIResponse::LogoutResponse();
        } catch (Exception $exception) {
            // An exception occurred during the logout process
            // Return an error response with the exception message and details
            return APIResponse::ErrorsResponse(__function__, $exception->getMessage(), $exception);
        }
    }

    public function login_redirect(): JsonResponse
    {
        // used as the redirect URL for any unauthorized request
        // just return an error response with HTTP Status Code 401 (Unauthorized)
        return APIResponse::ErrorsResponse('Unauthorized', 'Login using the login endpoint (/api/auth/login)', null, Response::HTTP_UNAUTHORIZED);
    }

    public function refresh_token(): JsonResponse
    {
        try {
            // get the authenticated user
            $user = auth('api')->user();
            // generate a new JWT Token
            $JWTToken = auth('api')->refresh();
            // return a success response with HTTP Status Code 200 (OK)
            return APIResponse::LoginResponse($JWTToken, $user);
        } catch (Exception $exception) {
            // An exception occurred during the refresh token process
            // Return an error response with the exception message and details
            return APIResponse::ErrorsResponse(__function__, $exception->getMessage(), $exception);
        }
    }
}
