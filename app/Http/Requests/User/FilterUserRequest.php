<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.list') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable','string','max:255'],
            'roles' => ['nullable','string','max:255'],
            'is_stream' => ['nullable','boolean'],
            'per_page' => ['nullable','integer','min:1','max:200'],
        ];
    }
}
