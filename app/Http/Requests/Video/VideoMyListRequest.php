<?php
// VideoMyListRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoMyListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('video.my.list') ?? false;
    }
    public function rules(): array
    {
        return [
            'per_page' => ['nullable','integer','min:1','max:100'],
        ];
    }
}
