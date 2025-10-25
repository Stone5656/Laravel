<?php
// app/Http/Requests/LiveStream/LiveStreamDeleteRequest.php

namespace App\Http\Requests\LiveStream;

use Illuminate\Foundation\Http\FormRequest;

class LiveStreamDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('livestream.delete') ?? false;
    }

    public function rules(): array
    {
        return [];
    }
}
