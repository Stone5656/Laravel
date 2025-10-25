<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserEmail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserEmailRepository
{
    public function createPending(User $user, string $email, int $ttlSeconds = 86400): UserEmail
    {
        $existing = UserEmail::where('user_id', $user->id)->where('email', $email)->first();
        if ($existing) {
            return $existing;
        }

        $token = (string) Str::uuid();
        $expiresAt = Carbon::now()->addSeconds($ttlSeconds);

        return UserEmail::create([
            'id'          => (string) Str::uuid(),
            'user_id'     => $user->id,
            'email'       => $email,
            'is_primary'  => false,
            'is_pending'  => true,
            'verify_token'=> $token,
            'expires_at'  => $expiresAt,
            'verified_at' => null,
        ]);
    }

    public function findPendingByToken(string $token): ?UserEmail
    {
        return UserEmail::where('verify_token', $token)
            ->where('is_pending', true)
            ->first();
    }

    public function markVerified(UserEmail $record): UserEmail
    {
        $record->verified_at = now();
        $record->is_pending = false;
        $record->verify_token = null;
        $record->expires_at = null;
        $record->save();

        return $record->refresh();
    }

    public function setPrimary(User $user, UserEmail $record): void
    {
        UserEmail::where('user_id', $user->id)->where('is_primary', true)->update(['is_primary' => false]);

        $record->is_primary = true;
        $record->save();

        // 任意：User側の同期（存在すれば）
        if (true) { /* プロジェクト固有のUserカラムに合わせて調整 */ }
    }
}
