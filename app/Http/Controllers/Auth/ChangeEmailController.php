<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\User\ChangeEmailRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChangeEmailController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/email/request",
     *     tags={"Auth"},
     *     summary="メールアドレス変更リクエスト",
     *     description="新しいメールアドレスへの確認メールを送信します。",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="new@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="確認メール送信完了")
     * )
     */
    public function request(ChangeEmailRequest $request, UserService $service): JsonResponse
    {
        $service->requestEmailChange(auth()->user(), $request->validated('email'));

        return response()->json(['message' => '確認メールを送信しました。']);
    }

    /**
     * @OA\Get(
     *     path="/api/email/confirm/{id}/{email}",
     *     tags={"Auth"},
     *     summary="メールアドレス変更の確定",
     *     description="送信されたメールリンクからメールアドレス変更を確定します。",
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="メールアドレス変更完了")
     * )
     */
    public function confirm(Request $request, $id, $email, UserService $service): JsonResponse
    {
        $user = User::findOrFail($id);
        $this->authorize('update', $user);

        $service->confirmEmailChange($user, $email);

        return response()->json(['message' => 'メールアドレスが変更されました。']);
    }
}
