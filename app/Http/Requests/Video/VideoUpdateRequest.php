<?php
// VideoUpdateRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.update') ?? false;
    }
    public function rules(): array
    {
        return [
            'title'          => ['sometimes','string','max:255'],
            'description'    => ['sometimes','nullable','string','max:5000'],
            'duration_sec'   => ['sometimes','nullable','integer','min:0'],
            'file_path'      => ['sometimes','nullable','string','max:1024'],
            'thumbnail_path' => ['sometimes','nullable','string','max:1024'],
            'is_public'      => ['sometimes','boolean'],
            'published_at'   => ['sometimes','nullable','date'],
        ];
    }
}
