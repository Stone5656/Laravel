<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\VerifyNewEmail;
use App\Enums\RoleEnum;

class UserService
{
    public function searchUsers(array $filters)
    {
        return User::query()
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%$name%"))
            ->when($filters['roles'] ?? null, fn($q, $role) => $q->where('roles', $role))
            ->when(isset($filters['is_streamer']), fn($q) => $q->where('is_streamer', $filters['is_streamer']))
            ->paginate(20);
    }

    public function updateProfile(User $user, array $data): User
    {
        $user->fill(array_filter(
            $data,
            fn($key) => in_array($key, ['name', 'bio', 'profile_image_path', 'cover_image_path', 'channel_name']),
            ARRAY_FILTER_USE_KEY
        ))->save();

        return $user;
    }

    public function updateRole(User $user, RoleEnum $role): User
    {
        $user->roles = $role;
        $user->save();

        return $user;
    }

    public function setStreamingStatus(User $user, bool $streaming): User
    {
        $user->is_streamer = $streaming;
        $user->save();

        return $user;
    }

    public function requestEmailChange(User $user, string $newEmail): void
    {
        $user->pending_email = $newEmail;
        $user->save();

        $user->notify(new VerifyNewEmail());
    }

    public function confirmEmailChange(User $user, string $email): void
    {
        if ($user->pending_email !== $email) {
            abort(403, '不正なリクエストです。');
        }

        $user->email = $email;
        $user->pending_email = null;
        $user->email_verified_at = null;
        $user->save();
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
