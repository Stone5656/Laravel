<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Get(
 *     path="/api/auth/verify-email/{id}/{hash}",
 *     tags={"Auth"},
 *     summary="メールアドレス確認",
 *     description="メール確認リンクからアクセスしたユーザーのメールアドレスを認証済みにします。",
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Parameter(name="hash", in="path", required=true, @OA\Schema(type="string")),
 *     @OA\Response(response=200, description="確認成功"),
 *     @OA\Response(response=422, description="リンクが無効または不正です")
 * )
 */
class VerifyEmailController extends Controller
{
    public function __invoke(EmailVerificationRequest $request): JsonResponse
    {
        // すでに認証済みなら成功メッセージ
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json([
                'message' => '既にメールアドレスは認証済みです。'
            ], 200);
        }

        // メール認証処理
        if ($request->user()->markEmailAsVerified()) {
            event(new Verified($request->user()));
        }

        return response()->json([
            'message' => 'メールアドレスが正常に認証されました。',
        ], 200);
    }
}
