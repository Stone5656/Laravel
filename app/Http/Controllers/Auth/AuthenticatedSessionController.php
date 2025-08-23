<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth"},
     *     summary="ログイン処理 (JWT)",
     *     description="ユーザーの認証を行い、JWTトークンを返却します。",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(response=200, description="ログイン成功"),
     *     @OA\Response(response=401, description="認証失敗（メールアドレスまたはパスワードが間違っています）"),
     * )
     */
    public function store(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');
        $guard = auth('api');

        if (! $token = $guard->attempt($credentials)) {
            throw new AuthenticationException('ログイン処理中の認証に失敗しました。');
        }

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $guard->factory()->getTTL() * 60,
            'user' => $guard->user(),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth"},
     *     summary="ログアウト処理 (JWT)",
     *     description="現在のJWTトークンを無効化します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="ログアウト成功"),
     *     @OA\Response(response=401, description="未認証"),
     * )
     */
    public function destroy(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'ログアウトしました'
        ]);
    }
}
