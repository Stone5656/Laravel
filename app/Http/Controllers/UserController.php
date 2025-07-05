<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UpdateStreamingRequest;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="User Management API",
 *     description="API for managing users, including profile updates, role management, and streaming settings."
 * )
 *
 * @OA\Tag(
 *     name="Users",
 *     description="User related operations"
 * )
 */
class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * @OA\Get(
     *     path="/api/users",
     *     tags={"Users"},
     *     summary="Get list of users",
     *     security={{"sanctum":{}}},
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function index(FilterUserRequest $request)
    {
        Gate::authorize('admin');
        $this->authorize('viewAny', User::class);

        $users = $this->userService->searchUsers($request->validated());

        return response()->json($users);
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/edit",
     *     tags={"Users"},
     *     summary="Get user profile for editing",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Successful operation")
     * )
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}/profile",
     *     tags={"Users"},
     *     summary="Update user profile",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="bio", type="string"),
     *             @OA\Property(property="profile_image_path", type="string"),
     *             @OA\Property(property="cover_image_path", type="string"),
     *             @OA\Property(property="channel_name", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Profile updated successfully")
     * )
     */
    public function updateProfile(UpdateProfileRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user = $this->userService->updateProfile($user, $request->validated());

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}/roles",
     *     tags={"Users"},
     *     summary="Update user role",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="roles", type="string", example="admin")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Role updated successfully")
     * )
     */
    public function updateRole(UpdateRoleRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user = $this->userService->updateRole($user, $request->validated('roles'));

        return response()->json($user);
    }

    /**
     * @OA\Put(
     *     path="/api/users/{user}/streaming",
     *     tags={"Users"},
     *     summary="Set user streaming status",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="is_streamer", type="boolean")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Streaming status updated")
     * )
     */
    public function setStreaming(UpdateStreamingRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user = $this->userService->setStreamingStatus($user, $request->validated('is_streamer'));

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *     path="/api/users/{user}",
     *     tags={"Users"},
     *     summary="Delete user",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="User deleted successfully")
     * )
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->deleteUser($user);

        return response()->json(['message' => 'ユーザーを削除しました。']);
    }
}
