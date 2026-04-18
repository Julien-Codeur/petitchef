<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDishRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isCook();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0.01',
            'available_qty' => 'required|integer|min:1',
            'served_date' => 'required|date|after_or_equal:today',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du plat est requis.',
            'price.required' => 'Le prix est requis.',
            'price.min' => 'Le prix doit être supérieur à 0.',
            'available_qty.required' => 'La quantité disponible est requise.',
            'served_date.required' => 'La date de service est requise.',
            'served_date.after_or_equal' => 'La date doit être d\'aujourd\'hui ou plus tard.',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif.',
            'photo.max' => 'L\'image ne doit pas dépasser 2 MB.',
        ];
    }
}
