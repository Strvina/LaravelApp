<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    // Ko sme da šalje ovaj request
    public function authorize()
    {
        // Trenutno svi ulogovani korisnici
        return auth()->check();
    }

    // Pravila validacije
    public function rules()
    {
        $productId = $this->route('id');

        return [
            'name' => 'required|string|max:64',
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($productId)],
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category' => 'nullable|string|max:64',
            'brand' => 'nullable|string|max:64',
            'description' => 'required|string|max:255|min:10',
            'image' => ['nullable', 'image', 'max:2048'],
        ];
    }

    // Opcionalno: prilagođene poruke grešaka
    public function messages()
    {
        return [
            'name.required' => 'Morate uneti ime proizvoda.',
            'price.required' => 'Cena je obavezna.',
            'stock.required' => 'Količina na stanju je obavezna.',
            'description.required' => 'Opis proizvoda je obavezan.',
            'description.min' => 'Opis mora imati minimum 10 karaktera.',
        ];
    }
}
