<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangeEmailRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * @OA\Tag(
 *     name="Auth",
 *     description="メールアドレス変更関連"
 * )
 */
class ChangeEmailController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/email/request",
     *     tags={"Auth"},
     *     summary="メールアドレス変更リクエスト",
     *     description="新しいメールアドレスへの確認メールを送信します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", example="new@example.com")
     *         )
     *     ),
     *     @OA\Response(response=200, description="確認メール送信完了"),
     *     @OA\Response(response=401, description="未認証"),
     * )
     */
    public function request(ChangeEmailRequest $request, UserService $service): JsonResponse
    {
        $user = auth('api')->user() ??  throw new AuthenticationException('メールアドレス変更リクエストのJWTが未検証です。');

        $service->requestEmailChange($user, $request->validated('email'));

        return response()->json(['message' => '確認メールを送信しました。']);
    }

    /**
     * @OA\Get(
     *     path="/api/email/confirm/{id}/{email}",
     *     tags={"Auth"},
     *     summary="メールアドレス変更の確定",
     *     description="確認メールのリンクからメールアドレス変更を確定します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Parameter(name="email", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="メールアドレス変更完了"),
     *     @OA\Response(response=403, description="アクセス拒否"),
     * )
     */
    public function confirm(Request $request, $id, $email, UserService $service): JsonResponse
    {
        $user = User::findOrFail($id);
    
        $authUser = auth('api')->user();
    
        if (! $authUser) {
            throw new AuthenticationException('メールアドレス変更確定処理中にJWT未認証が発見されました');
        }
    
        if ($authUser->id !== $user->id) {
            throw new AuthorizationException('この操作は許可されていません。');
        }
    
        $service->confirmEmailChange($user, $email);
    
        return response()->json(['message' => 'メールアドレスが変更されました。']);
    }
}
