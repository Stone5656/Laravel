<?php
namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserRules;

class UpdateRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'roles' => UserRules::system()['roles']
        ];
    }

    public function authorize(): bool
    {
        return true; // 必要に応じて認可チェック追加
    }
}
