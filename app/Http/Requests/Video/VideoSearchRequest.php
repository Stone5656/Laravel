<?php
// VideoSearchRequest.php
namespace App\Http\Requests\Video;

use Illuminate\Foundation\Http\FormRequest;

class VideoSearchRequest extends FormRequest
{
    public function authorize(): bool { return true; } // 公開API
    public function rules(): array
    {
        return [
            'title'    => ['nullable','string','max:255'],
            'sort'     => ['nullable','in:views_count,published_at'],
            'per_page' => ['nullable','integer','min:1','max:100'],
        ];
    }
}
