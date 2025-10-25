<?php
// app/Http/Requests/LiveStream/LiveStreamCreateRequest.php

namespace App\Http\Requests\LiveStream;

use Illuminate\Foundation\Http\FormRequest;

class LiveStreamCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('livestream.create') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'              => ['required','string','max:255'],
            'description'        => ['nullable','string','max:5000'],
            'thumbnail_path'     => ['nullable','string','max:1024'],
            'is_public'          => ['nullable','boolean'],
            'scheduled_start_at' => ['nullable','date'], // ISO-8601
        ];
    }
}
