<?php

namespace App\Http\Controllers;

use App\Models\AffiliationRequest;
use App\Models\Declaration;
use App\Models\Employer;
use App\Models\Employment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SdtReportController extends Controller
{
    public function contributions(Request $request): View
    {
        $filters = $this->periodFilters($request);

        $query = Declaration::query()
            ->with('employer:id,legal_name,affiliation_number')
            ->when($filters['year'], fn ($query, $year) => $query->where('period_year', $year))
            ->when($filters['month'], fn ($query, $month) => $query->where('period_month', $month));

        $summary = [
            'declarations' => (clone $query)->count(),
            'amount_due' => (float) (clone $query)->sum(DB::raw('COALESCE(global_total_payable, global_amount_due, total_declared_contribution, 0)')),
            'contributed' => (float) (clone $query)->sum(DB::raw('COALESCE(global_contribution_amount, total_declared_contribution, 0)')),
            'late_penalties' => (float) (clone $query)->sum(DB::raw('COALESCE(global_late_penalty_amount, 0)')),
            'anomalies' => (clone $query)
                ->where('contribution_entry_mode', 'GLOBAL')
                ->whereNotNull('global_contribution_amount')
                ->whereRaw('ABS(global_contribution_amount - COALESCE(global_total_payable, global_amount_due)) >= 0.01')
                ->count(),
            'overdue' => (clone $query)
                ->whereIn('status', ['DRAFT', 'SUBMITTED'])
                ->whereDate('due_date', '<', today()->toDateString())
                ->count(),
        ];

        $declarations = $query
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderByDesc('updated_at')
            ->paginate(15)
            ->withQueryString();

        return view('sdt.reports.contributions', [
            'filters' => $filters,
            'summary' => $summary,
            'declarations' => $declarations,
        ]);
    }

    public function activity(Request $request): View
    {
        $filters = $this->dateFilters($request);

        $affiliationQuery = AffiliationRequest::query()
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('created_at', '<=', $to));

        $declarationQuery = Declaration::query()
            ->when($filters['from'], fn ($query, $from) => $query->whereDate('created_at', '>=', $from))
            ->when($filters['to'], fn ($query, $to) => $query->whereDate('created_at', '<=', $to));

        $summary = [
            'employers' => Employer::query()->count(),
            'active_workers' => Employment::query()
                ->where('is_declared_active', true)
                ->distinct()
                ->count('worker_id'),
            'affiliations' => (clone $affiliationQuery)->count(),
            'affiliations_pending' => (clone $affiliationQuery)->where('status', 'PENDING')->count(),
            'affiliations_approved' => (clone $affiliationQuery)->where('status', 'APPROVED')->count(),
            'affiliations_rejected' => (clone $affiliationQuery)->where('status', 'REJECTED')->count(),
            'sdt_opinions_requested' => (clone $affiliationQuery)->whereNotNull('sdt_opinion_requested_at')->count(),
            'sdt_opinions_waiting' => (clone $affiliationQuery)
                ->whereNotNull('sdt_opinion_requested_at')
                ->whereNull('sdt_opinion_given_at')
                ->count(),
            'declarations' => (clone $declarationQuery)->count(),
            'declarations_draft' => (clone $declarationQuery)->where('status', 'DRAFT')->count(),
            'declarations_submitted' => (clone $declarationQuery)->where('status', 'SUBMITTED')->count(),
            'declarations_validated' => (clone $declarationQuery)->where('status', 'VALIDATED')->count(),
            'declarations_rejected' => (clone $declarationQuery)->where('status', 'REJECTED')->count(),
        ];

        $recentAffiliations = (clone $affiliationQuery)
            ->latest()
            ->limit(6)
            ->get();

        $recentDeclarations = (clone $declarationQuery)
            ->with('employer:id,legal_name')
            ->latest()
            ->limit(6)
            ->get();

        return view('sdt.reports.activity', [
            'filters' => $filters,
            'summary' => $summary,
            'recentAffiliations' => $recentAffiliations,
            'recentDeclarations' => $recentDeclarations,
        ]);
    }

    private function periodFilters(Request $request): array
    {
        $data = $request->validate([
            'year' => ['nullable', 'integer', 'min:2000', 'max:2100'],
            'month' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        return [
            'year' => $data['year'] ?? (int) now()->year,
            'month' => $data['month'] ?? null,
        ];
    }

    private function dateFilters(Request $request): array
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $data['from'] ?? Carbon::now()->startOfYear()->toDateString();

        return [
            'from' => $from,
            'to' => $data['to'] ?? now()->toDateString(),
        ];
    }
}
