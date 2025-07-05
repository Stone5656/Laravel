<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\{
    RegisteredUserController,
    AuthenticatedSessionController,
    PasswordResetLinkController,
    NewPasswordController,
    PasswordController,
    EmailVerificationNotificationController,
    VerifyEmailController
};
use Illuminate\Support\Facades\Route;

// 認証不要ルート（ゲスト用）
Route::middleware('guest')->group(function () {
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store']);
    Route::post('reset-password', [NewPasswordController::class, 'store']);
});

// 認証必要ルート
Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::put('password', [PasswordController::class, 'update']);

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1');

    Route::get('verify-email/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
        ->middleware(['signed', 'throttle:6,1']);

    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::put('/{user}/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('/{user}/roles', [UserController::class, 'updateRole'])->name('roles.update');
        Route::put('/{user}/streaming', [UserController::class, 'setStreaming'])->name('streaming.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
    });
});
