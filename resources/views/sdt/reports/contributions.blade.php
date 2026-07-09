@extends('layouts.app')

@section('title', 'Rapport cotisation SDT | CNSS')
@section('page_title', 'Rapport de cotisation')
@section('page_subtitle', 'Lecture SDT des montants dus, cotisés, pénalités et écarts')

@push('styles')
<style>
    .report { display: grid; gap: 1rem; }
    .panel { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); overflow: hidden; }
    .panel-head { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: 1rem; border-bottom: 1px solid #e4e7ec; flex-wrap: wrap; }
    .panel-head h2 { margin: 0; font-size: 1rem; color: #06346d; }
    .filters { display: flex; gap: .6rem; align-items: end; flex-wrap: wrap; }
    label { display: grid; gap: .3rem; color: #344054; font-size: .78rem; font-weight: 700; }
    input, select { border: 1px solid #d0d5dd; border-radius: 8px; padding: .52rem .65rem; font: inherit; min-width: 120px; }
    .btn { border: 0; border-radius: 8px; padding: .55rem .8rem; font: inherit; font-size: .82rem; font-weight: 800; cursor: pointer; background: #06346d; color: #fff; }
    .stats { display: grid; grid-template-columns: repeat(6, minmax(0, 1fr)); border: 1px solid #e4e7ec; border-radius: 8px; overflow: hidden; background: #fff; }
    .stat { padding: 1rem; border-right: 1px solid #e4e7ec; min-height: 105px; }
    .stat:last-child { border-right: 0; }
    .stat-label { margin: 0 0 .55rem; color: #667085; font-size: .78rem; font-weight: 700; }
    .stat-value { margin: 0; color: #101828; font-size: 1.35rem; font-weight: 800; word-break: break-word; }
    .stat-note { margin: .25rem 0 0; color: #667085; font-size: .76rem; }
    .hot .stat-value { color: #b42318; }
    .table-wrap { overflow: auto; }
    table { width: 100%; border-collapse: collapse; min-width: 980px; }
    th, td { text-align: left; padding: .78rem 1rem; border-bottom: 1px solid #e4e7ec; font-size: .84rem; color: #344054; }
    th { background: #f9fafb; color: #667085; text-transform: uppercase; font-size: .7rem; }
    td strong { color: #101828; }
    .badge { display: inline-flex; border-radius: 999px; padding: .22rem .55rem; font-size: .7rem; font-weight: 800; background: #e4f7f3; color: #006f66; }
    .badge-hot { background: #fef3f2; color: #b42318; }
    .badge-warn { background: #fffaeb; color: #b54708; }
    .pagination { padding: 1rem; }
    @media (max-width: 1100px) { .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .stat { border-bottom: 1px solid #e4e7ec; } }
    @media (max-width: 640px) { .stats { grid-template-columns: 1fr; } .stat { border-right: 0; } }
</style>
@endpush

@section('content')
@php
    $difference = $summary['contributed'] - $summary['amount_due'];
@endphp
<div class="report">
    <section class="panel">
        <div class="panel-head">
            <div>
                <h2>Synthese cotisation</h2>
            </div>
            <form class="filters" method="GET" action="{{ route('sdt.reports.contributions') }}">
                <label>Annee
                    <input type="number" name="year" min="2000" max="2100" value="{{ $filters['year'] }}">
                </label>
                <label>Mois
                    <select name="month">
                        <option value="">Tous</option>
                        @foreach(range(1, 12) as $month)
                            <option value="{{ $month }}" @selected((int) $filters['month'] === $month)>{{ str_pad((string) $month, 2, '0', STR_PAD_LEFT) }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="btn" type="submit">Filtrer</button>
            </form>
        </div>

        <div class="stats">
            <article class="stat">
                <p class="stat-label">Déclarations</p>
                <p class="stat-value">{{ number_format($summary['declarations'], 0, ',', ' ') }}</p>
                <p class="stat-note">dans la periode</p>
            </article>
            <article class="stat">
                <p class="stat-label">Montant dû</p>
                <p class="stat-value">{{ number_format($summary['amount_due'], 2, ',', ' ') }} CDF</p>
                <p class="stat-note">inclut penalites si calculees</p>
            </article>
            <article class="stat">
                <p class="stat-label">Montant cotisé</p>
                <p class="stat-value">{{ number_format($summary['contributed'], 2, ',', ' ') }} CDF</p>
                <p class="stat-note">montants declares comme verses</p>
            </article>
            <article class="stat {{ abs($difference) >= 0.01 ? 'hot' : '' }}">
                <p class="stat-label">Écart</p>
                <p class="stat-value">{{ number_format($difference, 2, ',', ' ') }} CDF</p>
                <p class="stat-note">{{ $difference < 0 ? 'insuffisance globale' : 'solde positif ou nul' }}</p>
            </article>
            <article class="stat">
                <p class="stat-label">Penalites retard</p>
                <p class="stat-value">{{ number_format($summary['late_penalties'], 2, ',', ' ') }} CDF</p>
                <p class="stat-note">majorations calculees</p>
            </article>
            <article class="stat {{ $summary['anomalies'] > 0 || $summary['overdue'] > 0 ? 'hot' : '' }}">
                <p class="stat-label">Alertes</p>
                <p class="stat-value">{{ number_format($summary['anomalies'] + $summary['overdue'], 0, ',', ' ') }}</p>
                <p class="stat-note">{{ $summary['anomalies'] }} écart(s), {{ $summary['overdue'] }} retard(s)</p>
            </article>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Détail des déclarations</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Employeur</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th>Du</th>
                    <th>Cotise</th>
                    <th>Écart</th>
                    <th>Penalite</th>
                    <th>Echeance</th>
                </tr>
                </thead>
                <tbody>
                @forelse($declarations as $declaration)
                    @php
                        $due = (float) ($declaration->global_total_payable ?? $declaration->global_amount_due ?? $declaration->total_declared_contribution ?? 0);
                        $paid = (float) ($declaration->global_contribution_amount ?? $declaration->total_declared_contribution ?? 0);
                        $rowDifference = $paid - $due;
                        $isOverdue = in_array($declaration->status, ['DRAFT', 'SUBMITTED'], true)
                            && $declaration->due_date
                            && $declaration->due_date->isPast();
                        $statusLabel = match ($declaration->status) {
                            'SUBMITTED' => 'Soumise',
                            'VALIDATED' => 'Validée',
                            'REJECTED' => 'Rejetée',
                            default => 'Brouillon',
                        };
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $declaration->employer?->legal_name ?? 'Employeur' }}</strong>
                            <div>{{ $declaration->employer?->affiliation_number ?? '-' }}</div>
                        </td>
                        <td>{{ str_pad((string) $declaration->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $declaration->period_year }}</td>
                        <td><span class="badge {{ $isOverdue ? 'badge-hot' : '' }}">{{ $isOverdue ? 'En retard' : $statusLabel }}</span></td>
                        <td>{{ number_format($due, 2, ',', ' ') }} CDF</td>
                        <td>{{ number_format($paid, 2, ',', ' ') }} CDF</td>
                        <td><span class="badge {{ abs($rowDifference) >= 0.01 ? 'badge-warn' : '' }}">{{ number_format($rowDifference, 2, ',', ' ') }} CDF</span></td>
                        <td>{{ number_format((float) $declaration->global_late_penalty_amount, 2, ',', ' ') }} CDF</td>
                        <td>{{ $declaration->due_date?->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8">Aucune déclaration pour cette période.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination">{{ $declarations->links() }}</div>
    </section>
</div>
@endsection
