<?php
namespace App\Rules;

use Illuminate\Validation\Rules\Enum;
use App\Enums\RoleEnum;

class UserRules
{
    public static function email(bool $unique = true): array
    {
        $rules = ['required', 'string', 'email', 'max:255'];
        if ($unique) {
            $rules[] = 'unique:users,email';
        }
        return $rules;
    }

    public static function password(): array
    {
        return ['required', 'string', 'min:8', 'confirmed'];
    }

    public static function profile(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:30'],
            'channel_name' => ['nullable', 'string', 'max:50', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_image_path' => ['nullable', 'image', 'max:2048'],
            'cover_image_path' => ['nullable', 'image', 'max:4096'],
        ];
    }

    public static function system(): array
    {
        return [
            'is_streamer' => ['required', 'boolean'],
            'roles' => ['required', new Enum(RoleEnum::class)],
        ];
    }
}
