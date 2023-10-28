<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Repositories\UserRepository;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Responses\APIResponse;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;


class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function signup(RegisterRequest $request): JsonResponse
    {
        // get validated data
        $data = $request->validated();

        // create user
        $user = (new UserRepository())->create(
            $data['email'],
            $data['name'],
            $data['password'],
            $data['is_active'],
            $data['type']
        );

        // fire registered event
        event(new Registered($user));

        // return created successfully response
        return APIResponse::CreatedSuccessfully(UserResource::make($user), 'User Registered successfully');
    }
}
