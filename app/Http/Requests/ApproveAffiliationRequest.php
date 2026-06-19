<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApproveAffiliationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'affiliation_number' => ['required', 'string', 'max:30', 'unique:employers,affiliation_number'],
            'note' => ['nullable', 'string'],
        ];
    }
}
