<?php

namespace App\Http\Requests\Auth;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string'],
            'remember' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'L\'email est requis.',
            'email.email' => 'Veuillez entrer une adresse email valide.',
            'email.exists' => 'Cet email n\'existe pas.',
            'password.required' => 'Le mot de passe est requis.',
        ];
    }
}

