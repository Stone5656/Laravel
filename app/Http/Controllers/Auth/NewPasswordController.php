<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reset-password/{token}",
     *     tags={"Auth"},
     *     summary="パスワードリセット画面の表示",
     *     description="パスワードリセットリンクからアクセスしたユーザーに、新しいパスワードを入力する画面を表示します。",
     *     @OA\Parameter(
     *         name="token",
     *         in="path",
     *         required=true,
     *         description="パスワードリセット用トークン",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="リセット画面を表示")
     * )
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * @OA\Post(
     *     path="/api/reset-password",
     *     tags={"Auth"},
     *     summary="パスワードをリセットする",
     *     description="リセット用トークンと新しいパスワードを受け取り、ユーザーのパスワードを変更します。",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(response=302, description="リセット成功時にログイン画面にリダイレクト"),
     *     @OA\Response(response=422, description="リセット失敗時のバリデーションエラー")
     * )
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
