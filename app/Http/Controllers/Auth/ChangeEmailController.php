<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use App\Http\Requests\User\ChangeEmailRequest;
use Illuminate\Http\Request;

class ChangeEmailController extends Controller
{
    public function request(ChangeEmailRequest $request, UserService $service)
    {
        $service->requestEmailChange(auth()->user(), $request->validated('email'));

        return back()->with('status', '確認メールを送信しました。');
    }

    public function confirm(Request $request, $id, $email, UserService $service)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user); // セキュリティ強化

        $service->confirmEmailChange($user, $email);

        return redirect()->route('dashboard')->with('status', 'メールアドレスが変更されました。');
    }
}
