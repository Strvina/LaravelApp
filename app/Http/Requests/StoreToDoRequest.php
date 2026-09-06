<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreToDoRequest extends FormRequest
{
    // Ko sme da šalje request
    public function authorize()
    {
        return auth()->check(); // samo ulogovani korisnici
    }

    // Pravila validacije
    public function rules()
    {
        return [
            'task' => 'required|string|min:3|max:255',
            'priority' => ['required', Rule::in(['low', 'medium', 'high'])],
            'is_recurring' => 'nullable|boolean',
            'recurrence' => [
                'nullable',
                Rule::requiredIf($this->boolean('is_recurring')),
                Rule::in(['daily', 'weekly', 'monthly']),
            ],
        ];
    }

    // Opcionalno: prilagođene poruke grešaka
    public function messages()
    {
        return [
            'task.required' => 'Morate uneti zadatak.',
            'task.min' => 'Zadatak mora imati minimum 3 karaktera.',
            'priority.required' => 'Morate odabrati prioritet.',
            'priority.in' => 'Prioritet mora biti low, medium ili high.',
            'recurrence.in' => 'Ponavljanje može biti daily, weekly ili monthly.',
        ];
    }
}
