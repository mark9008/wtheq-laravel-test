<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\Profile\EditUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $users = (new UserRepository())->list();
        return APIResponse::DataResponse(UserResource::collection($users));
    }

    /**
     * Display the specified resource.
     */
    public function show(): JsonResponse
    {
        $user = (new UserRepository())->get(auth('api')->user()->id);
        return APIResponse::DataResponse(UserResource::make($user));
    }

    public function update(EditUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        if (isset($data['avatar'])) {
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }
        $user = (new UserRepository())->update(auth('api')->user()->id, $data);
        return APIResponse::DataResponse(UserResource::make($user));
    }

    public function destroy(): JsonResponse
    {
        $user = (new UserRepository())->get(auth('api')->user()->id);
        if ($user->delete())
            return APIResponse::SuccessResponse('User deleted successfully');
        return APIResponse::ErrorsResponse('Error deleting user', '', 500);
    }
}
