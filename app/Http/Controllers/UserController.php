<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\User\FilterUserRequest;
use App\Http\Requests\User\UpdateProfileRequest;
use App\Http\Requests\User\UpdateRoleRequest;
use App\Http\Requests\User\UpdateStreamingRequest;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * @OA\Get(
     *   path="/api/users",
     *   tags={"Users"},
     *   summary="ユーザー一覧の取得",
     *   description="管理者が全ユーザーの一覧を取得します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Response(
     *     response=200,
     *     description="ユーザー一覧の取得に成功しました",
     *     @OA\JsonContent(ref="#/components/schemas/PaginatedUser")
     *   ),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません")
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
     *   path="/api/users/{user}/edit",
     *   tags={"Users"},
     *   summary="ユーザー編集用データの取得",
     *   description="指定したユーザーIDに対応するプロフィール情報を取得します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="user", in="path", required=true, description="対象ユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(response=200, description="ユーザー情報の取得に成功しました", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=404, description="ユーザーが見つかりません")
     * )
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);

        return view('users.edit', compact('user'));
    }

    /**
     * @OA\Put(
     *   path="/api/users/{user}/profile",
     *   tags={"Users"},
     *   summary="ユーザープロフィールの更新",
     *   description="指定したユーザーのプロフィール情報（名前、自己紹介、画像等）を更新します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="user", in="path", required=true, description="更新対象のユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="name", type="string", description="ユーザー名", maxLength=255),
     *       @OA\Property(property="bio", type="string", description="自己紹介文", maxLength=1000),
     *       @OA\Property(property="profile_image_path", type="string", description="プロフィール画像パス"),
     *       @OA\Property(property="cover_image_path", type="string", description="カバー画像パス"),
     *       @OA\Property(property="channel_name", type="string", description="チャンネル名", maxLength=255)
     *     )
     *   ),
     *   @OA\Response(response=200, description="プロフィールの更新に成功しました", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=422, description="バリデーションエラー")
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
     *   path="/api/users/{user}/roles",
     *   tags={"Users"},
     *   summary="ユーザーのロール（役割）の変更",
     *   description="指定したユーザーのロール（admin, userなど）を変更します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="user", in="path", required=true, description="変更対象のユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="roles", type="string", example="admin", description="新しいロール")
     *     )
     *   ),
     *   @OA\Response(response=200, description="ロールの更新に成功しました", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=422, description="バリデーションエラー")
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
     *   path="/api/users/{user}/streaming",
     *   tags={"Users"},
     *   summary="配信ステータスの変更",
     *   description="指定したユーザーの配信ステータス（配信者かどうか）を更新します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="user", in="path", required=true, description="配信ステータス変更対象のユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\RequestBody(
     *     required=true,
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="is_stream", type="boolean", description="配信者フラグ true/false")
     *     )
     *   ),
     *   @OA\Response(response=200, description="配信ステータスの更新に成功しました", @OA\JsonContent(ref="#/components/schemas/User")),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=422, description="バリデーションエラー")
     * )
     */
    public function setStreaming(UpdateStreamingRequest $request, User $user)
    {
        $this->authorize('update', $user);

        $user = $this->userService->setStreamingStatus($user, $request->validated('is_stream'));

        return response()->json($user);
    }

    /**
     * @OA\Delete(
     *   path="/api/users/{user}",
     *   tags={"Users"},
     *   summary="ユーザーの削除",
     *   description="指定したユーザーアカウントを削除します。",
     *   security={{"bearerAuth":{}}},
     *   @OA\Parameter(
     *     name="user", in="path", required=true, description="削除対象のユーザーID(UUID)",
     *     @OA\Schema(type="string", format="uuid")
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="ユーザーの削除に成功しました",
     *     @OA\JsonContent(
     *       type="object",
     *       @OA\Property(property="message", type="string", example="ユーザーを削除しました。")
     *     )
     *   ),
     *   @OA\Response(response=401, description="認証エラー"),
     *   @OA\Response(response=403, description="権限がありません"),
     *   @OA\Response(response=404, description="ユーザーが見つかりません")
     * )
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $this->userService->deleteUser($user);

        return response()->json(['message' => 'ユーザーを削除しました。']);
    }
}
