<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/verify-email",
     *     tags={"Auth"},
     *     summary="メール認証プロンプト表示",
     *     description="メールアドレスが未認証のユーザーに、認証を促すプロンプト画面を表示するAPIです。",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=302,
     *         description="すでに認証済みの場合はダッシュボードへリダイレクト"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="認証を促すビューを返します"
     *     )
     * )
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        return $request->user()->hasVerifiedEmail()
                    ? redirect()->intended(route('dashboard', absolute: false))
                    : view('auth.verify-email');
    }
}
