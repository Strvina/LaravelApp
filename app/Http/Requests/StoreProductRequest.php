<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => 'required|string|max:64',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|numeric|min:0',
            'description' => 'required|string|max:255|min:10',
        ];
    }

    // Opcionalno: prilagođene poruke grešaka
    public function messages()
    {
        return [
            'name.required' => 'Morate uneti ime proizvoda.',
            'price.required' => 'Cena je obavezna.',
            'quantity.required' => 'Količina je obavezna.',
            'description.required' => 'Opis proizvoda je obavezan.',
            'description.min' => 'Opis mora imati minimum 10 karaktera.',
        ];
    }
}
