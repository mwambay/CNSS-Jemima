<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDeclarationRequest;
use App\Http\Requests\UpdateDeclarationRequest;
use App\Http\Requests\UpsertDeclarationLineRequest;
use App\Models\Declaration;
use App\Models\DeclarationLine;
use App\Models\Employment;
use App\Services\ContributionCalculationService;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class DeclarationController extends Controller
{
    public function __construct(private readonly ContributionCalculationService $contributionCalculationService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Declaration::query()
            ->with('employer')
            ->withCount('declarationLines')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderByDesc('id');

        if ($request->filled('employer_id')) {
            $query->where('employer_id', (int) $request->query('employer_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }

        if ($request->filled('period_year')) {
            $query->where('period_year', (int) $request->query('period_year'));
        }

        if ($request->filled('period_month')) {
            $query->where('period_month', (int) $request->query('period_month'));
        }

        $items = $query->get()->map(fn (Declaration $declaration): array => $this->toSummary($declaration));

        return response()->json($items);
    }

    public function store(StoreDeclarationRequest $request): JsonResponse
    {
        $data = $request->validated();

        $dueDate = $data['due_date'] ?? Carbon::create($data['period_year'], $data['period_month'], 1)
            ->endOfMonth()
            ->format('Y-m-d');

        $declaration = Declaration::query()->create([
            'employer_id' => $data['employer_id'],
            'period_year' => $data['period_year'],
            'period_month' => $data['period_month'],
            'due_date' => $dueDate,
            'status' => 'DRAFT',
            'total_declared_salary' => 0,
            'total_declared_contribution' => 0,
        ]);

        $declaration->load('employer');

        return response()->json($this->toSummary($declaration), 201);
    }

    public function show(Declaration $declaration): JsonResponse
    {
        $declaration->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    public function update(UpdateDeclarationRequest $request, Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        $declaration->update($request->validated());
        $declaration->refresh()->load('employer')->loadCount('declarationLines');

        return response()->json($this->toSummary($declaration));
    }

    public function destroy(Declaration $declaration): Response
    {
        $this->ensureDraft($declaration);

        $declaration->delete();

        return response()->noContent();
    }

    public function submit(Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        $hasGlobalAmount = $declaration->contribution_entry_mode === 'GLOBAL'
            && $declaration->global_contribution_amount !== null;

        if (!$hasGlobalAmount && $declaration->declarationLines()->count() === 0) {
            abort(422, 'Impossible de soumettre une declaration sans ligne.');
        }

        $declaration->update([
            'status' => 'SUBMITTED',
            'submitted_at' => now(),
        ]);

        $declaration->refresh()->load('employer')->loadCount('declarationLines');

        return response()->json($this->toSummary($declaration));
    }

    public function validateDeclaration(Request $request, Declaration $declaration): JsonResponse
    {
        if ($declaration->status !== 'SUBMITTED') {
            abort(422, 'Seules les declarations soumises peuvent etre validees.');
        }

        $declaration->update([
            'status' => 'VALIDATED',
            'validation_message' => $request->input('validation_message'),
        ]);

        $declaration->refresh()->load('employer')->loadCount('declarationLines');

        return response()->json($this->toSummary($declaration));
    }

    public function rejectDeclaration(Request $request, Declaration $declaration): JsonResponse
    {
        if ($declaration->status !== 'SUBMITTED') {
            abort(422, 'Seules les declarations soumises peuvent etre rejetees.');
        }

        $message = (string) $request->input('validation_message', 'Declaration rejetee.');

        $declaration->update([
            'status' => 'REJECTED',
            'validation_message' => $message,
        ]);

        $declaration->refresh()->load('employer')->loadCount('declarationLines');

        return response()->json($this->toSummary($declaration));
    }

    public function upsertLine(UpsertDeclarationLineRequest $request, Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        if ($declaration->contribution_entry_mode === 'GLOBAL') {
            abort(422, 'Revenez au mode detaille avant d ajouter une ligne de travailleur.');
        }

        $data = $request->validated();

        $isLinkedToEmployer = Employment::query()
            ->where('employer_id', $declaration->employer_id)
            ->where('worker_id', $data['worker_id'])
            ->where('is_declared_active', true)
            ->exists();

        if (!$isLinkedToEmployer) {
            abort(422, 'Le travailleur doit etre rattache a cet employeur.');
        }

        DB::transaction(function () use ($declaration, $data): void {
            DeclarationLine::query()->updateOrCreate(
                [
                    'declaration_id' => $declaration->id,
                    'worker_id' => $data['worker_id'],
                ],
                [
                    'gross_salary' => $data['gross_salary'],
                    'contributable_salary' => $data['contributable_salary'],
                    'worked_days' => $data['worked_days'] ?? null,
                    'anomaly_flag' => (bool) ($data['anomaly_flag'] ?? false),
                    'anomaly_reason' => $data['anomaly_reason'] ?? null,
                ]
            );

            $this->recalculateTotals($declaration);
        });

        $declaration->refresh()->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    public function destroyLine(Declaration $declaration, DeclarationLine $declarationLine): JsonResponse
    {
        $this->ensureDraft($declaration);

        if ($declarationLine->declaration_id !== $declaration->id) {
            abort(404);
        }

        DB::transaction(function () use ($declaration, $declarationLine): void {
            $declarationLine->delete();
            $this->recalculateTotals($declaration);
        });

        $declaration->refresh()->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    public function recalculate(Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        if ($declaration->contribution_entry_mode === 'GLOBAL') {
            abort(422, 'Une declaration globale ne peut pas etre recalculee par travailleur.');
        }

        DB::transaction(function () use ($declaration): void {
            $this->recalculateTotals($declaration);
        });

        $declaration->refresh()->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    public function previewGlobalContribution(Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        return response()->json([
            'calculation' => $this->contributionCalculationService->previewGlobalContribution($declaration),
        ]);
    }

    public function recordGlobalContribution(Request $request, Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        $data = $request->validate([
            'contributed_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $calculation = $this->contributionCalculationService->previewGlobalContribution($declaration);
        $contributedAmount = round((float) $data['contributed_amount'], 2);

        $declaration->update([
            'contribution_entry_mode' => 'GLOBAL',
            'global_contribution_amount' => $contributedAmount,
            'global_amount_due' => $calculation['amount_due'],
            'global_salary_envelope' => $calculation['salary_envelope'],
            'global_employer_rate' => $calculation['employer_rate'],
            'global_worker_rate' => $calculation['worker_rate'],
            'global_worker_count' => $calculation['worker_count'],
            'total_declared_salary' => $calculation['salary_envelope'],
            'total_declared_contribution' => $contributedAmount,
        ]);

        $declaration->refresh()->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    public function useDetailedEntry(Declaration $declaration): JsonResponse
    {
        $this->ensureDraft($declaration);

        DB::transaction(function () use ($declaration): void {
            $declaration->update([
                'contribution_entry_mode' => 'DETAILED',
                'global_contribution_amount' => null,
                'global_amount_due' => null,
                'global_salary_envelope' => null,
                'global_employer_rate' => null,
                'global_worker_rate' => null,
                'global_worker_count' => null,
            ]);

            if ($declaration->declarationLines()->exists()) {
                $this->recalculateTotals($declaration);
                return;
            }

            $declaration->update([
                'total_declared_salary' => 0,
                'total_declared_contribution' => 0,
            ]);
        });

        $declaration->refresh()->load([
            'employer',
            'declarationLines' => function ($query): void {
                $query->with(['worker', 'contributionCalc.rate'])->orderBy('id');
            },
        ]);

        return response()->json($this->toDetails($declaration));
    }

    private function ensureDraft(Declaration $declaration): void
    {
        if ($declaration->status !== 'DRAFT') {
            abort(422, 'Cette action est uniquement autorisee pour une declaration en brouillon.');
        }
    }

    private function recalculateTotals(Declaration $declaration): void
    {
        $this->contributionCalculationService->recalculateDeclaration($declaration);
    }

    private function toSummary(Declaration $declaration): array
    {
        $dueDate = $declaration->due_date;
        $dueDateValue = $dueDate instanceof CarbonInterface
            ? $dueDate->format('Y-m-d')
            : (is_string($dueDate) && $dueDate !== '' ? substr($dueDate, 0, 10) : null);

        return [
            'id' => $declaration->id,
            'employer_id' => $declaration->employer_id,
            'employer_name' => $declaration->employer?->legal_name,
            'period_year' => $declaration->period_year,
            'period_month' => $declaration->period_month,
            'status' => $declaration->status,
            'contribution_entry_mode' => $declaration->contribution_entry_mode ?? 'DETAILED',
            'global_contribution_amount' => $declaration->global_contribution_amount,
            'global_amount_due' => $declaration->global_amount_due,
            'global_contribution_difference' => $this->globalContributionDifference($declaration),
            'global_contribution_status' => $this->globalContributionStatus($declaration),
            'global_salary_envelope' => $declaration->global_salary_envelope,
            'global_employer_rate' => $declaration->global_employer_rate,
            'global_worker_rate' => $declaration->global_worker_rate,
            'global_worker_count' => $declaration->global_worker_count,
            'due_date' => $dueDateValue,
            'submitted_at' => $declaration->submitted_at?->format('Y-m-d H:i:s'),
            'total_declared_salary' => $declaration->total_declared_salary,
            'total_declared_contribution' => $declaration->total_declared_contribution,
            'lines_count' => $declaration->declaration_lines_count ?? $declaration->declarationLines()->count(),
            'validation_message' => $declaration->validation_message,
        ];
    }

    private function toDetails(Declaration $declaration): array
    {
        return [
            ...$this->toSummary($declaration),
            'lines' => $declaration->declarationLines->map(function (DeclarationLine $line): array {
                $contributionCalc = $line->contributionCalc;
                $rate = $contributionCalc?->rate;

                return [
                    'id' => $line->id,
                    'worker_id' => $line->worker_id,
                    'worker_name' => trim(($line->worker?->first_name ?? '').' '.($line->worker?->last_name ?? '')),
                    'worker_ssn' => $line->worker?->social_security_number,
                    'gross_salary' => $line->gross_salary,
                    'contributable_salary' => $line->contributable_salary,
                    'worked_days' => $line->worked_days,
                    'anomaly_flag' => $line->anomaly_flag,
                    'anomaly_reason' => $line->anomaly_reason,
                    'employer_amount' => $contributionCalc?->employer_amount,
                    'worker_amount' => $contributionCalc?->worker_amount,
                    'total_contribution' => $contributionCalc?->total_amount,
                    'applied_rate' => $rate === null ? null : [
                        'id' => $rate->id,
                        'regime_code' => $rate->regime_code,
                        'employer_rate' => $rate->employer_rate,
                        'worker_rate' => $rate->worker_rate,
                    ],
                ];
            })->values(),
        ];
    }

    private function globalContributionDifference(Declaration $declaration): ?float
    {
        if ($declaration->global_amount_due === null || $declaration->global_contribution_amount === null) {
            return null;
        }

        return round(
            (float) $declaration->global_contribution_amount - (float) $declaration->global_amount_due,
            2
        );
    }

    private function globalContributionStatus(Declaration $declaration): ?string
    {
        $difference = $this->globalContributionDifference($declaration);

        if ($difference === null) {
            return null;
        }

        if (abs($difference) < 0.01) {
            return 'CONFORME';
        }

        return $difference < 0 ? 'INSUFFISANT' : 'SUPERIEUR';
    }
}
