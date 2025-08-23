<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class EmailVerificationNotificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     tags={"Auth"},
     *     summary="メール認証リンク再送信",
     *     description="未認証のユーザーにメール認証リンクを送信します。",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="メール認証リンク送信完了"),
     *     @OA\Response(response=302, description="すでにメール認証済み"),
     *     @OA\Response(response=401, description="認証が必要です"),
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth('api')->user() ?? throw new AuthenticationException('メール認証リクエストのJWTが未検証です');

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'すでにメール認証が完了しています。'], 302);
        }

        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'メール認証リンクを送信しました。']);
    }
}
