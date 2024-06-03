<?php

namespace App\Http\Requests\User;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'name' => 'sometimes|max:255|string',
            'email' => 'nullable|email|max:255',
            'user_id' => 'required|int',
            'picture' => 'required|base64dimensions:min_width=100,min_height=200,max_width=400,max_height=500'
        ];
    }
}
