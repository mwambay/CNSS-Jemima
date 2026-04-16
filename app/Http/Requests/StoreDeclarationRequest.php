<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDeclarationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employer_id' => ['required', 'integer', 'exists:employers,id'],
            'period_year' => ['required', 'integer', 'between:2000,2100'],
            'period_month' => ['required', 'integer', 'between:1,12'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
