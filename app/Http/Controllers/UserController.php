<?php

namespace App\Http\Controllers;

use App\Http\Repositories\UserRepository;
use App\Http\Requests\Profile\EditUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\APIResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Display a list of the users.
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);

        // get users from repository
        $userRepo = new UserRepository();
        $users = $userRepo->list($active_only);

        // return a data response with the UserResource collection
        return APIResponse::DataResponse(UserResource::collection($users));
    }

    /**
     * Display the current user profile.
     * @return JsonResponse
     */
    public function show(): JsonResponse
    {
        // Get current user id
        $id = auth('api')->user()->id;

        // Get user from the repository
        $userRepo = new UserRepository();
        $user = $userRepo->get($id);

        // Return a data response with the UserResource
        return APIResponse::DataResponse(UserResource::make($user));
    }

    /**
     * Update the user profile.
     * @param EditUserRequest $request
     * @return JsonResponse
     */
    public function update(EditUserRequest $request): JsonResponse
    {
        // Get validated data
        $data = $request->validated();

        // Check if avatar is uploaded
        if (isset($data['avatar'])) {
            // Store avatar in storage/public/avatars folder and return its path
            $data['avatar'] = $data['avatar']->store('avatars', 'public');
        }
        // get current user id
        $id = auth('api')->user()->id;

        // Update user using the repository
        $userRepo = new UserRepository();
        $user = $userRepo->update($id, $data);

        // Return a data response with the UserResource
        return APIResponse::DataResponse(UserResource::make($user));
    }

    /**
     * Deactivate the specified user by id.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        // cast id to integer
        $id = (int)$id;

        // Get current user
        $user = auth('api')->user();

        // Check if user is gold (assumed that only gold users can delete users)
        if ($user->type != 'gold') {
            // Return an error response with status code 403 (Forbidden)
            return APIResponse::ErrorsResponse('You are not allowed to delete users', '', status: Response::HTTP_FORBIDDEN);
        }

        // create userRepository instance and delete user with the given id
        $userRepo = new UserRepository();
        $deleted = $userRepo->delete($id);

        if ($deleted) {
            // return success response if user deleted successfully
            return APIResponse::SuccessResponse('User deleted successfully');
        }

        // return error response if user not deleted
        return APIResponse::ErrorsResponse('Error deleting user', '', status: 500);
    }

    /**
     * Search for users by type.
     * @param Request $request
     * @param string $type
     * @return JsonResponse
     */
    public function searchByType(Request $request, string $type): JsonResponse
    {
        // get active_only query parameter
        $active_only = $request->query('active_only', true);

        // get users from repository
        $userRepo = new UserRepository();
        $users = $userRepo->searchByType($type, $active_only);

        // return a data response with the UserResource collection
        return APIResponse::DataResponse(UserResource::collection($users));
    }
}
