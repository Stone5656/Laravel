<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/email/verification-notification",
     *     tags={"Auth"},
     *     summary="メール認証リンクを再送信",
     *     description="ユーザーがメール認証を完了していない場合に、認証リンクを再送信するAPIです。",
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=302,
     *         description="すでに認証済みの場合はダッシュボードへリダイレクト"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="メール認証リンクを再送信しました"
     *     )
     * )
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
