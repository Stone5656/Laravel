<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    /**
     * 検索条件に基づきユーザー一覧を返す
     *
     * @param array $filters
     * @return LengthAwarePaginator|Collection
     */
    public function search(array $filters): LengthAwarePaginator
    {
        $query = User::query()
            ->when($filters['name'] ?? null, fn($q, $name) => $q->where('name', 'like', "%{$name}%"))
            ->when($filters['roles'] ?? null, fn($q, $roles) => $q->where('roles', $roles))
            ->when(array_key_exists('is_stream', $filters), fn($q) => $q->where('is_stream', (bool)$filters['is_stream']));

        $perPage = (int)($filters['per_page'] ?? 20);
        return $query->paginate($perPage);
    }

    public function updateProfile(User $user, array $data): User
    {
        $fillable = [
            'name','bio','profile_image_path','cover_image_path','channel_name',
        ];
        foreach ($fillable as $f) {
            if (array_key_exists($f, $data)) {
                $user->{$f} = $data[$f];
            }
        }
        $user->save();
        return $user->refresh();
    }

    public function updateRole(User $user, string $role): User
    {
        $user->roles = $role;
        $user->save();
        return $user->refresh();
    }

    public function updateStreaming(User $user, bool $isStream): User
    {
        $user->is_stream = $isStream;
        $user->save();
        return $user->refresh();
    }

    public function setPendingEmail(User $user, string $pendingEmail): User
    {
        $user->pending_email = $pendingEmail;
        $user->save();
        return $user->refresh();
    }

    public function applyEmail(User $user, string $email): User
    {
        $user->email = $email;
        $user->pending_email = null;
        $user->email_verified_at = null;
        $user->save();
        return $user->refresh();
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
