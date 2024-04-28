<?php

namespace App\User\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Request\HttpDataRequest;

class LoginRequest extends HttpDataRequest
{
    public function messages(): array
    {
        return [
          'email.required' => 'Mot de passe requis !',
          'email.email' => 'Entrez une adresse email valide !',
          'password.required' => 'Mot de passe invalide !',
          'password.min' => 'Le mot de passe doit contenir au moins 8 caractères !'
        ];
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string|min:4'
        ];
    }
}
