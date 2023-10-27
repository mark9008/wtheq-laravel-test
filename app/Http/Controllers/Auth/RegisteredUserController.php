<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Responses\APIResponse;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function signup(RegisterRequest $request): JsonResponse
    {
//        return APIResponse::SuccessResponse('User Registered successfully, Confirmation mail has been sent');
        $data = $request->validated();

        $user = (new UserRepository())->create(
            $data['email'],
            $data['name'],
            $data['password'],
            $data['is_active'],
            $data['type']);


        event(new Registered($user));

        return APIResponse::CreatedSuccessfully(UserResource::make($user), 'User Registered successfully');
    }
}
