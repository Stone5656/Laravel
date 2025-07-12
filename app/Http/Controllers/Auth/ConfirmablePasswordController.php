<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;

/**
 * パスワード確認用コントローラー
 *
 * 特定の機密操作を行う前に、ユーザーのパスワードを再確認するAPIです。
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * パスワード確認API
     *
     * @OA\Post(
     *     path="/api/password/confirm",
     *     tags={"Auth"},
     *     summary="パスワード確認",
     *     description="機密操作（メール変更・アカウント削除など）の前に、ユーザーのパスワードを再確認します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"password"},
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="パスワード確認成功"),
     *     @OA\Response(response=401, description="認証が必要です"),
     *     @OA\Response(response=422, description="パスワードが正しくありません")
     * )
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // JWT未認証ならエラー
        $user = auth('api')->user() ?? throw new AuthenticationException('パスワード確認処理中にJWT未認証が発見されました');

        // パスワードが一致するか確認
        if (! auth('api')->validate([
            'email' => $user->email,
            'password' => $request->password,
        ])) {
            throw ValidationException::withMessages([
                'password' => 'パスワードが正しくありません。',
            ]);
        }

        return response()->json(['message' => 'パスワード確認成功']);
    }
}
