<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isClient();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.dish_id' => 'required|integer|exists:dishes,id',
            'items.*.quantity' => 'required|integer|min:1',
            'pickup_time' => 'required|date_format:H:i',
            'note_client' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom validation messages
     */
    public function messages(): array
    {
        return [
            'items.required' => 'Veuillez ajouter au moins un plat à votre commande.',
            'items.min' => 'La commande doit contenir au moins un plat.',
            'items.*.dish_id.required' => 'L\'ID du plat est requis.',
            'items.*.dish_id.exists' => 'Le plat sélectionné n\'existe pas.',
            'items.*.quantity.required' => 'La quantité est requise.',
            'items.*.quantity.min' => 'La quantité doit être au moins 1.',
            'pickup_time.required' => 'L\'heure de retrait est requise.',
            'pickup_time.date_format' => 'L\'heure doit être au format HH:mm.',
        ];
    }
}
