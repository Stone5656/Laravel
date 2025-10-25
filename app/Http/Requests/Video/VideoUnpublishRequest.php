<?php
// VideoUnpublishRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoUnpublishRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.unpublish') ?? false;
    }
    public function rules(): array { return []; }
}
