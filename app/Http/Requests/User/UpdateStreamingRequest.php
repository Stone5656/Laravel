<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserRules;

class UpdateStreamingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_stream' => UserRules::system()['is_stream']
        ];
    }

    public function authorize(): bool
    {
        return true; // 必要に応じて認可チェック追加
    }
}
