<?php
// VideoDeleteRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoDeleteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.delete') ?? false;
    }
    public function rules(): array { return []; }
}
