<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.updateProfile') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes','string','max:255'],
            'bio' => ['sometimes','string','max:1000'],
            'profile_image_path' => ['sometimes','string','max:1024'],
            'cover_image_path' => ['sometimes','string','max:1024'],
            'channel_name' => ['sometimes','string','max:255'],
        ];
    }
}
