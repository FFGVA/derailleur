<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdhesionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:60'],
            'prenom' => ['required', 'string', 'max:40'],
            'email' => ['required', 'email'],
            'telephone' => ['required', 'string', 'max:20'],
            'photo_ok' => ['required', 'string'],
            'type_velo' => ['nullable', 'string'],
            'sorties' => ['nullable', 'string'],
            'atelier' => ['nullable', 'string'],
            'instagram' => ['nullable', 'string'],
            'strava' => ['nullable', 'string'],
            'statuts_ok' => ['nullable', 'string'],
            'cotisation_ok' => ['nullable', 'string'],
            'website' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom est obligatoire.',
            'nom.max' => 'Le nom ne doit pas dépasser 60 caractères.',
            'prenom.required' => 'Le prénom est obligatoire.',
            'prenom.max' => 'Le prénom ne doit pas dépasser 40 caractères.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'telephone.required' => 'Le numéro de téléphone est obligatoire.',
            'telephone.max' => 'Le numéro de téléphone ne doit pas dépasser 20 caractères.',
            'photo_ok.required' => 'L\'autorisation photos/vidéos est obligatoire.',
        ];
    }
}
