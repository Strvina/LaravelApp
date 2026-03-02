<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check(); // samo ulogovani korisnici mogu slati ovaj requestx
    }

    public function rules(): array
    {
        return [
            "name"=>"required|string|max:255",
            "amount"=>"required|numeric",
            "type"=>"required|in:income,expense",
            "date"=>"required|date"
        ];


    }
}
