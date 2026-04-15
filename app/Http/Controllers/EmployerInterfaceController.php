<?php

namespace App\Http\Controllers;

use App\Models\Employer;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class EmployerInterfaceController extends Controller
{
    public function index(): View
    {
        return view('employers.index');
    }

    public function show(Employer $employer): View
    {
        $canManageWorkers = auth()->user()?->roles()->where('code', 'ADMIN')->exists() ?? false;

        $employer->load([
            'employments' => function ($query): void {
                $query->with('worker')->orderByDesc('start_date');
            },
        ]);

        $workers = $employer->employments
            ->filter(fn ($employment): bool => $employment->worker !== null)
            ->groupBy('worker_id')
            ->map(function (Collection $items): array {
                $latestEmployment = $items->first();
                $worker = $latestEmployment->worker;

                return [
                    'id' => $worker->id,
                    'full_name' => trim(($worker->first_name ?? '').' '.($worker->last_name ?? '')),
                    'social_security_number' => $worker->social_security_number,
                    'national_id' => $worker->national_id,
                    'status' => $worker->status,
                    'contract_type' => $latestEmployment->contract_type,
                    'start_date' => $latestEmployment->start_date?->format('Y-m-d'),
                    'base_salary' => $latestEmployment->base_salary,
                ];
            })
            ->values();

        return view('employers.show', [
            'employer' => $employer,
            'workers' => $workers,
            'canManageWorkers' => $canManageWorkers,
        ]);
    }
}
