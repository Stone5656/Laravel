<?php
// app/Http/Requests/LiveStream/LiveStreamUpdateRequest.php

namespace App\Http\Requests\LiveStream;

use Illuminate\Foundation\Http\FormRequest;

class LiveStreamUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('livestream.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'title'              => ['sometimes','string','max:255'],
            'description'        => ['sometimes','nullable','string','max:5000'],
            'thumbnail_path'     => ['sometimes','nullable','string','max:1024'],
            'is_public'          => ['sometimes','boolean'],
            'status'             => ['sometimes','string','in:SCHEDULED,LIVE,ENDED,CANCELLED'],
            'scheduled_start_at' => ['sometimes','nullable','date'],
        ];
    }
}
