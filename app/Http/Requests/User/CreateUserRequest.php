<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email',
            'picture' => 'sometimes|image|mimes:jpeg,jpg,png|required|max:10000',
            'has_kids' => 'required|boolean',
            'country_id' => 'integer|required|exists:countries,id',
            'colours_id' => 'required|array',
            'colours_id.*' => 'required|integer|min:1|exists:colours,id',
            'password' => [
                'required',
                'string',
                'min:8',              // Minimum length of 8 characters
                'max:35',             // Maximum length of 35 characters
                'regex:/[a-z]/',      // Must contain at least one lowercase letter
                'regex:/[A-Z]/',      // Must contain at least one uppercase letter
                'regex:/[0-9]/',      // Must contain at least one digit
                'regex:/[@$!%*#?&]/', // Must contain a special character
            ],
            'password_confirmation' => 'required|same:password'
        ];

    }
}
