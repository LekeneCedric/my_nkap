<?php

namespace App\User\Infrastructure\Http\Requests;

use App\Shared\Infrastructure\Request\HttpDataRequest;

class RegisterUserRequest extends HttpDataRequest
{
    public function messages(): array
    {
        return [
            'email.required' => 'L\'addresse email est requis !',
            'email.email' => 'Entrez une addresse email valide !',
//            'email.unique' => 'Un autre utilisateur existe avec cette adresse email !',
            'password.required' => 'Le mot de passe est requis !',
            'username.required' => 'Le nom d\'utilisateur est requis !',
            'username.max' => 'Le nom d\'utilisateur ne dois pas dépasser 25 caractères !',
            'birthday.required' => 'La date de naissance est requise !',
            'birthday.date' => 'Entrez une date de naissance valide !',
            'professionId.required' => 'Veuillez sélectionner la proféssion',
        ];
    }

    public function rules(): array
    {
        return [
//            'email' => 'required|email|unique:users',
            'email' => 'required|email',
            'password' => 'required|string',
            'username' => 'required|string|max:25',
            'birthday' => 'required|date',
            'professionId' => 'required|string',
        ];
    }
}
