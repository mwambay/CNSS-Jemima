<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWorkerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_security_number' => ['required', 'string', 'max:50', 'unique:workers,social_security_number'],
            'national_id' => ['nullable', 'string', 'max:50'],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['M', 'F', 'OTHER'])],
            'status' => ['nullable', Rule::in(['ACTIVE', 'SUSPENDED', 'INACTIVE'])],
            'employer_id' => ['required', 'integer', 'exists:employers,id'],
            'employment_start_date' => ['required', 'date'],
            'contract_type' => ['nullable', 'string', 'max:30'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
