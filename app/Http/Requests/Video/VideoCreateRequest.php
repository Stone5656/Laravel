<?php
// VideoCreateRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.create') ?? false;
    }
    public function rules(): array
    {
        return [
            'title'          => ['required','string','max:255'],
            'description'    => ['nullable','string','max:5000'],
            'duration_sec'   => ['nullable','integer','min:0'],
            'file_path'      => ['nullable','string','max:1024'],
            'thumbnail_path' => ['nullable','string','max:1024'],
            'is_public'      => ['nullable','boolean'],
            'published_at'   => ['nullable','date'], // ISO-8601
        ];
    }
}
