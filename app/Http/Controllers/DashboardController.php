<?php

namespace App\Http\Controllers;

use App\Models\AffiliationRequest;
use App\Models\ContributionRate;
use App\Models\Declaration;
use App\Models\Employer;
use App\Models\Employment;
use App\Models\FraudAlert;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = today();
        $currentYear = (int) $today->year;
        $canManageBusiness = auth()->user()?->roles()
            ->whereIn('code', ['ADMIN', 'AGENT_SES'])
            ->exists() ?? false;
        $isAdmin = auth()->user()?->roles()->where('code', 'ADMIN')->exists() ?? false;

        $pendingDeclarationQuery = Declaration::query()
            ->whereIn('status', ['DRAFT', 'SUBMITTED']);

        $stats = [
            'active_employers' => Employer::query()->where('status', 'ACTIVE')->count(),
            'active_workers' => Employment::query()
                ->where('is_declared_active', true)
                ->distinct()
                ->count('worker_id'),
            'pending_declarations' => (clone $pendingDeclarationQuery)->count(),
            'year_contributions' => (float) Declaration::query()
                ->where('period_year', $currentYear)
                ->sum('total_declared_contribution'),
        ];

        $operationalCounts = [
            'pending_affiliations' => AffiliationRequest::query()->where('status', 'PENDING')->count(),
            'overdue_declarations' => (clone $pendingDeclarationQuery)
                ->whereDate('due_date', '<', $today->toDateString())
                ->count(),
            'contribution_anomalies' => Declaration::query()
                ->where('contribution_entry_mode', 'GLOBAL')
                ->whereNotNull('global_amount_due')
                ->whereNotNull('global_contribution_amount')
                ->whereColumn('global_amount_due', '<>', 'global_contribution_amount')
                ->count(),
            'open_fraud_alerts' => FraudAlert::query()->where('status', 'OPEN')->count(),
            'missing_salaries' => Employment::query()
                ->where('is_declared_active', true)
                ->whereNull('base_salary')
                ->count(),
        ];

        $trend = $this->buildContributionTrend($today);
        $alerts = $this->buildAlerts($today, $operationalCounts, $canManageBusiness);
        $suggestions = $this->buildSuggestions($today, $operationalCounts, $isAdmin);

        $recentDeclarations = Declaration::query()
            ->with('employer:id,legal_name')
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->orderByDesc('updated_at')
            ->limit(6)
            ->get();

        return view('dashboard', [
            'stats' => $stats,
            'operationalCounts' => $operationalCounts,
            'trend' => $trend,
            'trendMax' => max(1, (float) $trend->max('amount')),
            'alerts' => $alerts,
            'suggestions' => $suggestions,
            'recentDeclarations' => $recentDeclarations,
            'canManageBusiness' => $canManageBusiness,
            'isAdmin' => $isAdmin,
        ]);
    }

    private function buildContributionTrend(Carbon $today): Collection
    {
        return collect(range(5, 0))->map(function (int $monthsAgo) use ($today): array {
            $month = $today->copy()->startOfMonth()->subMonths($monthsAgo);

            return [
                'label' => ucfirst($month->locale('fr')->translatedFormat('M')),
                'period' => $month->format('m/Y'),
                'amount' => (float) Declaration::query()
                    ->where('period_year', $month->year)
                    ->where('period_month', $month->month)
                    ->sum('total_declared_contribution'),
            ];
        });
    }

    private function buildAlerts(Carbon $today, array $counts, bool $canManageBusiness): Collection
    {
        $alerts = collect();

        $anomalies = Declaration::query()
            ->with('employer:id,legal_name')
            ->where('contribution_entry_mode', 'GLOBAL')
            ->whereNotNull('global_amount_due')
            ->whereNotNull('global_contribution_amount')
            ->whereColumn('global_amount_due', '<>', 'global_contribution_amount')
            ->orderByDesc('updated_at')
            ->limit(3)
            ->get();

        foreach ($anomalies as $declaration) {
            $difference = (float) $declaration->global_contribution_amount - (float) $declaration->global_amount_due;
            $alerts->push([
                'level' => 'warning',
                'title' => $difference < 0 ? 'Cotisation insuffisante' : 'Cotisation superieure',
                'message' => sprintf(
                    '%s - %02d/%d : ecart de %s CDF.',
                    $declaration->employer?->legal_name ?? 'Employeur',
                    $declaration->period_month,
                    $declaration->period_year,
                    number_format(abs($difference), 2, ',', ' ')
                ),
                'href' => $canManageBusiness ? route('declarations.show', $declaration) : null,
            ]);
        }

        if ($counts['overdue_declarations'] > 0) {
            $alerts->push([
                'level' => 'critical',
                'title' => 'Declarations en retard',
                'message' => $counts['overdue_declarations'].' declaration(s) non finalisee(s) apres echeance.',
                'href' => $canManageBusiness ? route('declarations.interface') : null,
            ]);
        }

        if ($counts['open_fraud_alerts'] > 0) {
            $alerts->push([
                'level' => 'critical',
                'title' => 'Alertes de controle ouvertes',
                'message' => $counts['open_fraud_alerts'].' alerte(s) necessitent une analyse.',
                'href' => null,
            ]);
        }

        if ($counts['pending_affiliations'] > 0) {
            $alerts->push([
                'level' => 'info',
                'title' => 'Affiliations en attente',
                'message' => $counts['pending_affiliations'].' demande(s) attendent une decision.',
                'href' => $canManageBusiness ? route('affiliations.index') : null,
            ]);
        }

        return $alerts->take(6)->values();
    }

    private function buildSuggestions(Carbon $today, array $counts, bool $isAdmin): Collection
    {
        $suggestions = collect();

        if ($counts['missing_salaries'] > 0) {
            $suggestions->push([
                'title' => 'Completer les salaires de base',
                'message' => $counts['missing_salaries'].' rattachement(s) actif(s) sans salaire bloquent le calcul global.',
                'href' => route('workers.interface'),
            ]);
        }

        if ($counts['pending_affiliations'] > 0) {
            $suggestions->push([
                'title' => 'Traiter les demandes les plus anciennes',
                'message' => 'Commencez par les affiliations encore en attente de verification.',
                'href' => route('affiliations.index'),
            ]);
        }

        $hasCurrentRate = ContributionRate::query()
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $today->toDateString())
            ->where(function ($query) use ($today): void {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $today->toDateString());
            })
            ->exists();

        if ($isAdmin && !$hasCurrentRate) {
            $suggestions->push([
                'title' => 'Configurer le taux courant',
                'message' => 'Aucune modalite active ne couvre la periode actuelle.',
                'href' => route('contribution-rates.index'),
            ]);
        }

        if ($suggestions->isEmpty()) {
            $suggestions->push([
                'title' => 'Situation operationnelle stable',
                'message' => 'Aucune action corrective prioritaire n est detectee pour le moment.',
                'href' => null,
            ]);
        }

        return $suggestions->take(3)->values();
    }
}
