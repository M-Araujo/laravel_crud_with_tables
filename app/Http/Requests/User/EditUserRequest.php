<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class EditUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|string',
            'email' => 'required|email|max:255',
            'picture' => 'sometimes|image|mimes:jpeg,jpg,png|required|max:10000',
            'has_kids' => 'required|boolean',
            'password' => [
                'nullable',
                'string',
                'min:8',              // Minimum length of 8 characters
                'max:35',             // Maximum length of 35 characters
                'regex:/[a-z]/',      // Must contain at least one lowercase letter
                'regex:/[A-Z]/',      // Must contain at least one uppercase letter
                'regex:/[0-9]/',      // Must contain at least one digit
                'regex:/[@$!%*#?&]/', // Must contain a special character
            ],
            'password_confirm' => 'sometimes|same:password'
        ];
    }
}
