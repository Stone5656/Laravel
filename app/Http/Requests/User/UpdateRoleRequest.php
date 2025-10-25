<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.updateRole') ?? false;
    }

    public function rules(): array
    {
        return [
            'role' => ['required','string','max:64'],
        ];
    }
}
