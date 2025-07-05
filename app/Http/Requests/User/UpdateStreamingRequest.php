<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserRules;

class UpdateStreamingRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'is_streamer' => UserRules::system()['is_streamer']
        ];
    }

    public function authorize(): bool
    {
        return true; // 必要に応じて認可チェック追加
    }
}
