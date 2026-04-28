<?php

namespace App\Services;

use App\Models\ContributionCalc;
use App\Models\ContributionRate;
use App\Models\Declaration;
use App\Models\DeclarationLine;
use Carbon\Carbon;

class ContributionCalculationService
{
    public function recalculateDeclaration(Declaration $declaration): void
    {
        $declaration->loadMissing('declarationLines', 'employer');

        $rate = $this->resolveApplicableRate($declaration);
        $lines = $declaration->declarationLines;

        $totalSalary = (float) $lines->sum(static fn (DeclarationLine $line): float => (float) $line->gross_salary);

        if ($rate === null) {
            $totalContribution = (float) $lines->sum(static fn (DeclarationLine $line): float => (float) $line->contributable_salary);
            $this->clearLineCalculations($lines->pluck('id')->all());

            $declaration->update([
                'total_declared_salary' => round($totalSalary, 2),
                'total_declared_contribution' => round($totalContribution, 2),
            ]);

            return;
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

    private function clearLineCalculations(array $lineIds): void
    {
        if ($lineIds === []) {
            return;
        }

        ContributionCalc::query()
            ->whereIn('declaration_line_id', $lineIds)
            ->delete();
    }
}
