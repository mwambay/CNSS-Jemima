<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'affiliation_number' => ['required', 'string', 'max:30', 'unique:employers,affiliation_number'],
            'legal_name' => ['required', 'string', 'max:200'],
            'tax_id' => ['nullable', 'string', 'max:50', 'unique:employers,tax_id'],
            'registration_number' => ['nullable', 'string', 'max:50'],
            'legal_form' => ['nullable', 'string', 'max:50'],
            'sector' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['ACTIVE', 'SUSPENDED', 'CLOSED'])],
            'verification_status' => ['nullable', Rule::in(['PENDING', 'VERIFIED', 'REJECTED'])],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'address' => ['nullable', 'string'],
        ];
    }
}
