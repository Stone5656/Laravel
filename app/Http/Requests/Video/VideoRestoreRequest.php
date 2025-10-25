<?php
// VideoRestoreRequest.php（管理者専用）
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoRestoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.restore') ?? false;
    }
    public function rules(): array { return []; }
}
