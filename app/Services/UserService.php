<?php

namespace App\Services;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Notifications\VerifyNewEmail;
use App\Enums\RoleEnum;

class UserService
{    
    public function __construct(protected UserRepository $users) {}
    public function searchUsers(array $filters)
    {
        return $this->users->search($filters) /* delegating to repository */
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%$name%"))
            ->when($filters['roles'] ?? null, fn($q, $role) => $q->where('roles', $role))
            ->when(isset($filters['is_stream']), fn($q) => $q->where('is_stream', $filters['is_stream']))
            /* pagination handled in repository */ ->paginate(20);
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
        $user->is_stream = $streaming;
        $user->save();

        return $user;
    }

    public function deleteUser(User $user): void
    {
        $user->delete();
    }
}
