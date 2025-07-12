<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\UserRules;

class ChangeEmailRequest extends FormRequest
{
    public function rules(): array
    {
        return UserRules::email();
    }

    public function authorize(): bool
    {
        return false;
    }
}
