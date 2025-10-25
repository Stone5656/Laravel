<?php
// app/Http/Requests/LiveStream/LiveStreamRescheduleRequest.php

namespace App\Http\Requests\LiveStream;

use Illuminate\Foundation\Http\FormRequest;

class LiveStreamRescheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('livestream.reschedule') ?? false;
    }

    public function rules(): array
    {
        return [
            'scheduled_start_at' => ['required','date'], // ISO-8601
            'status'             => ['nullable','string','in:SCHEDULED,LIVE,ENDED,CANCELLED'],
        ];
    }
}
