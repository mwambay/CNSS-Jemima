<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertDeclarationLineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'worker_id' => ['required', 'integer', 'exists:workers,id'],
            'gross_salary' => ['required', 'numeric', 'min:0'],
            'contributable_salary' => ['required', 'numeric', 'min:0'],
            'worked_days' => ['nullable', 'integer', 'min:0', 'max:31'],
            'anomaly_flag' => ['nullable', 'boolean'],
            'anomaly_reason' => ['nullable', 'string'],
        ];
    }
}
