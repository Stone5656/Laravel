<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class FilterUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:50'],
            'roles' => ['nullable', 'string'],
            'is_streamer' => ['nullable', 'boolean'],
        ];
    }

    public function authorize(): bool
    {
        return true; // 管理者制限をかけたい場合は適宜変更
    }
}
