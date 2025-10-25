<?php
// VideoPublishRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoPublishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.publish') ?? false;
    }
    public function rules(): array
    {
        return [
            'published_at' => ['nullable','date'], // ISO-8601
        ];
    }
}
