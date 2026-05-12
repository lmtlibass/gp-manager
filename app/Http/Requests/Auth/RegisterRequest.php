<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    protected $errorBag = 'registerBag';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Agence
            'agency_name'    => ['required', 'string', 'max:100'],
            'agency_phone'   => ['required', 'string', 'max:30'],
            'agency_city'    => ['required', 'string', 'max:100'],
            'agency_country' => ['required', 'string', 'max:100'],

            // Compte GP Manager
            'first_name'             => ['required', 'string', 'max:100'],
            'last_name'              => ['required', 'string', 'max:100'],
            'phone'                  => ['nullable', 'string', 'max:30'],
            'email'                  => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'               => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation'  => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'agency_name.required'    => "Le nom de l'agence est obligatoire.",
            'agency_phone.required'   => "Le téléphone de l'agence est obligatoire.",
            'agency_city.required'    => "La ville est obligatoire.",
            'agency_country.required' => "Le pays est obligatoire.",
            'first_name.required'     => 'Le prénom est obligatoire.',
            'last_name.required'      => 'Le nom est obligatoire.',
            'email.required'          => "L'adresse email est obligatoire.",
            'email.email'             => "L'adresse email n'est pas valide.",
            'email.unique'            => 'Cette adresse email est déjà utilisée.',
            'password.required'       => 'Le mot de passe est obligatoire.',
            'password.min'            => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'      => 'Les mots de passe ne correspondent pas.',
        ];
    }
}
