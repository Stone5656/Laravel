<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStreamingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('user.updateStreaming') ?? false;
    }

    public function rules(): array
    {
        return [
            'is_stream' => ['required','boolean'],
        ];
    }
}
