<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * メール認証プロンプトAPI
 */
class EmailVerificationPromptController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/verify-email",
     *     tags={"Auth"},
     *     summary="メール認証確認",
     *     description="認証済みか確認し、未認証の場合はプロンプトを返します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="未認証"),
     *     @OA\Response(response=302, description="認証済み（ダッシュボード想定）")
     * )
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?? abort(401, '認証が必要です。');

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'すでにメール認証済みです。'], 302);
        }

        return response()->json(['message' => 'メール認証が必要です。'], 200);
    }
}
