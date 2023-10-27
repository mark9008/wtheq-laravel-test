<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\EditUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\APIResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = (new UserRepository())->list();
        return APIResponse::DataResponse(UserResource::collection($users));
    }

    /**
     * Display the specified resource.
     */
    public function show()
    {
        $user = (new UserRepository())->get(auth()->user()->id);
        return APIResponse::DataResponse(UserResource::make($user));
    }

    public function update(EditUserRequest $request): User
    {
        $data = $request->validated();
        if (isset($data['avatar'])) {
            $data['avatar'] = $data['avatar']->store('avatars');
        }
        return (new UserRepository())->update(auth()->user()->id, $data);
    }

    public function destroy(): JsonResponse
    {
        $user = (new UserRepository())->get(auth()->user()->id);
        if ($user->delete())
            return APIResponse::SuccessResponse('User deleted successfully');
        return APIResponse::ErrorsResponse('Error deleting user', '', 500);
    }
}
