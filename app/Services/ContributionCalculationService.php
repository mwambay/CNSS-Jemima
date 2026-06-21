<?php

namespace App\Services;

use App\Models\ContributionCalc;
use App\Models\ContributionRate;
use App\Models\Declaration;
use App\Models\DeclarationLine;
use App\Models\Employment;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class ContributionCalculationService
{
    public function previewGlobalContribution(Declaration $declaration): array
    {
        $rate = $this->resolveApplicableRate($declaration);

        if ($rate === null) {
            throw ValidationException::withMessages([
                'contribution_rate' => sprintf(
                    'Aucune modalite de cotisation active ne couvre la periode %02d/%d.',
                    $declaration->period_month,
                    $declaration->period_year
                ),
            ]);
        }

        $employments = Employment::query()
            ->with('worker')
            ->where('employer_id', $declaration->employer_id)
            ->where('is_declared_active', true)
            ->orderByDesc('start_date')
            ->get()
            ->unique('worker_id')
            ->values();

        if ($employments->isEmpty()) {
            throw ValidationException::withMessages([
                'workers' => 'Aucun travailleur actif n est rattache a cet employeur.',
            ]);
        }

        $missingSalaryNames = $employments
            ->filter(fn (Employment $employment): bool => $employment->base_salary === null)
            ->map(function (Employment $employment): string {
                $name = trim(($employment->worker?->first_name ?? '').' '.($employment->worker?->last_name ?? ''));

                return $name !== '' ? $name : 'Matricule '.($employment->worker?->social_security_number ?? $employment->worker_id);
            })
            ->values();

        if ($missingSalaryNames->isNotEmpty()) {
            throw ValidationException::withMessages([
                'base_salary' => 'Salaire de base manquant pour: '.$missingSalaryNames->implode(', ').'.',
            ]);
        }

        $salaryEnvelope = round((float) $employments->sum('base_salary'), 2);
        $employerRate = (float) $rate->employer_rate;
        $workerRate = (float) $rate->worker_rate;
        $totalRate = round($employerRate + $workerRate, 4);
        $amountDue = round($salaryEnvelope * $totalRate / 100, 2);

        return [
            'worker_count' => $employments->count(),
            'salary_envelope' => $salaryEnvelope,
            'employer_rate' => $employerRate,
            'worker_rate' => $workerRate,
            'total_rate' => $totalRate,
            'amount_due' => $amountDue,
        ];
    }

    public function recalculateDeclaration(Declaration $declaration): void
    {
        $declaration->loadMissing('declarationLines', 'employer');

        $rate = $this->resolveApplicableRate($declaration);
        $lines = $declaration->declarationLines;

        $totalSalary = (float) $lines->sum(static fn (DeclarationLine $line): float => (float) $line->gross_salary);

        if ($rate === null) {
            throw ValidationException::withMessages([
                'contribution_rate' => sprintf(
                    'Aucune modalite de cotisation active ne couvre la periode %02d/%d.',
                    $declaration->period_month,
                    $declaration->period_year
                ),
            ]);
        }

        $totalContribution = 0.0;
        foreach ($lines as $line) {
            $base = $this->applyBounds((float) $line->contributable_salary, $rate);
            $employerAmount = round($base * ((float) $rate->employer_rate) / 100, 2);
            $workerAmount = round($base * ((float) $rate->worker_rate) / 100, 2);
            $lineTotal = round($employerAmount + $workerAmount, 2);

            ContributionCalc::query()->updateOrCreate(
                ['declaration_line_id' => $line->id],
                [
                    'rate_id' => $rate->id,
                    'employer_amount' => $employerAmount,
                    'worker_amount' => $workerAmount,
                    'total_amount' => $lineTotal,
                    'calculated_at' => now(),
                ]
            );

            $totalContribution += $lineTotal;
        }

        $declaration->update([
            'total_declared_salary' => round($totalSalary, 2),
            'total_declared_contribution' => round($totalContribution, 2),
        ]);
    }

    private function resolveApplicableRate(Declaration $declaration): ?ContributionRate
    {
        $periodDate = Carbon::create($declaration->period_year, $declaration->period_month, 1)->toDateString();

        return ContributionRate::query()
            ->where('is_active', true)
            ->where('effective_from', '<=', $periodDate)
            ->where(function ($query) use ($periodDate): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $periodDate);
            })
            ->orderByRaw('CASE WHEN regime_code = ? THEN 0 ELSE 1 END', ['GENERAL'])
            ->orderByDesc('effective_from')
            ->orderByDesc('id')
            ->first();
    }

    private function applyBounds(float $base, ContributionRate $rate): float
    {
        $bounded = $base;

        if ($rate->floor_amount !== null) {
            $bounded = max($bounded, (float) $rate->floor_amount);
        }

        if ($rate->ceiling_amount !== null) {
            $bounded = min($bounded, (float) $rate->ceiling_amount);
        }

        return round($bounded, 2);
    }

}
