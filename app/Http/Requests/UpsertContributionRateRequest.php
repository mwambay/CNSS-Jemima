<?php

namespace App\Http\Requests;

use App\Models\ContributionRate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpsertContributionRateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'regime_code' => strtoupper(trim((string) $this->input('regime_code'))),
            'is_active' => $this->boolean('is_active'),
        ]);
    }

    public function rules(): array
    {
        return [
            'regime_code' => ['required', 'string', 'max:50'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'employer_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'worker_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'floor_amount' => ['nullable', 'numeric', 'min:0'],
            'ceiling_amount' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $employerRate = (float) $this->input('employer_rate', 0);
            $workerRate = (float) $this->input('worker_rate', 0);

            if ($employerRate + $workerRate > 100) {
                $validator->errors()->add('worker_rate', 'La somme des taux ne peut pas depasser 100 %.');
            }

            $floor = $this->input('floor_amount');
            $ceiling = $this->input('ceiling_amount');

            if ($floor !== null && $floor !== '' && $ceiling !== null && $ceiling !== '' && (float) $floor > (float) $ceiling) {
                $validator->errors()->add('ceiling_amount', 'Le plafond doit etre superieur ou egal au plancher.');
            }

            if (!$this->boolean('is_active') || $validator->errors()->isNotEmpty()) {
                return;
            }

            $effectiveFrom = (string) $this->input('effective_from');
            $effectiveTo = $this->input('effective_to');
            $currentRate = $this->route('contributionRate');

            $overlapExists = ContributionRate::query()
                ->where('regime_code', (string) $this->input('regime_code'))
                ->where('is_active', true)
                ->when($currentRate instanceof ContributionRate, fn ($query) => $query->where('id', '!=', $currentRate->id))
                ->where(function ($query) use ($effectiveTo): void {
                    if ($effectiveTo === null || $effectiveTo === '') {
                        return;
                    }

                    $query->where('effective_from', '<=', $effectiveTo);
                })
                ->where(function ($query) use ($effectiveFrom): void {
                    $query->whereNull('effective_to')
                        ->orWhere('effective_to', '>=', $effectiveFrom);
                })
                ->exists();

            if ($overlapExists) {
                $validator->errors()->add('effective_from', 'Une modalite active du meme regime couvre deja cette periode.');
            }
        });
    }
}
