<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\Profile\EditUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);
        $users = (new UserRepository())->list($active_only);
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

    public function destroy($id): JsonResponse
    {
        $user = auth('api')->user();
        if ($user->type != 'gold')
            return APIResponse::ErrorsResponse('You are not allowed to delete users', '', status: 403);
        $deleted = (new UserRepository())->delete($id);
        if ($deleted)
            return APIResponse::SuccessResponse('User deleted successfully');
        return APIResponse::ErrorsResponse('Error deleting user', '', status: 500);
    }

    public function searchByType(Request $request,$type): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);
        $users = (new UserRepository())->searchByType($type,$active_only);
        return APIResponse::DataResponse(UserResource::collection($users));
    }
}
