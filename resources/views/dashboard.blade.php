@extends('layouts.app')

@section('title', 'Tableau de bord | CNSS')
@section('page_title', 'Tableau de bord')
@section('page_subtitle', 'Vue operationnelle au '.now()->format('d/m/Y'))

@push('styles')
<style>
    .dashboard { display: grid; gap: 1rem; }
    .dashboard-panel { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); }
    .panel-heading { display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem 1.1rem; border-bottom: 1px solid #e4e7ec; }
    .panel-heading h2 { margin: 0; color: #101828; font-size: 1rem; }
    .panel-heading p { margin: .25rem 0 0; color: #667085; font-size: .82rem; }
    .section-link { color: #06346d; font-size: .82rem; font-weight: 700; text-decoration: none; }
    .section-link:hover { text-decoration: underline; }

    .priority-banner { display: grid; grid-template-columns: auto minmax(0, 1fr) auto; gap: 1rem; align-items: center; padding: 1rem 1.1rem; border-radius: 8px; border: 1px solid #fedf89; background: #fffaeb; box-shadow: 0 8px 22px rgba(181, 71, 8, .08); }
    .priority-banner.critical { border-color: #fecdca; background: #fff6f5; box-shadow: 0 8px 22px rgba(180, 35, 24, .08); }
    .priority-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 50%; background: #f79009; color: #fff; font-weight: 800; font-size: 1.25rem; }
    .priority-banner.critical .priority-icon { background: #d92d20; }
    .priority-banner h2 { margin: 0; color: #101828; font-size: 1rem; }
    .priority-banner p { margin: .25rem 0 0; color: #344054; font-size: .84rem; line-height: 1.45; }
    .priority-action { justify-self: end; display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: .55rem .85rem; border-radius: 8px; background: #06346d; color: #fff; font-size: .8rem; font-weight: 700; text-decoration: none; white-space: nowrap; }
    .priority-action:hover { background: #042b5d; }

    .overview-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .stat { min-width: 0; padding: 1.1rem; border-right: 1px solid #e4e7ec; }
    .stat:last-child { border-right: 0; }
    .stat-label { margin: 0; color: #667085; font-size: .82rem; font-weight: 500; }
    .stat-value { margin: .5rem 0 .25rem; color: #06346d; font-size: clamp(1.45rem, 2.4vw, 2rem); font-weight: 700; line-height: 1; }
    .stat-note { margin: 0; color: #667085; font-size: .76rem; }
    .stat-note.attention { color: #b54708; font-weight: 700; }

    .attention-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .75rem; padding: 1rem; border-top: 1px solid #e4e7ec; background: #fbfcfe; }
    .attention-card { min-width: 0; display: grid; gap: .45rem; padding: .9rem; border: 1px solid #d0d5dd; border-radius: 8px; color: inherit; text-decoration: none; background: #fff; transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease; }
    .attention-card:hover { transform: translateY(-1px); border-color: #008f83; box-shadow: 0 8px 18px rgba(6, 52, 109, .08); }
    .attention-card.is-hot { border-color: #fecdca; background: #fff6f5; }
    .attention-card.is-warning { border-color: #fedf89; background: #fffcf5; }
    .attention-card.is-info { border-color: #99e7dd; background: #f1fffc; }
    .attention-top { display: flex; align-items: center; justify-content: space-between; gap: .5rem; }
    .attention-label { color: #344054; font-size: .74rem; font-weight: 700; text-transform: uppercase; }
    .attention-pill { border-radius: 999px; padding: .2rem .45rem; color: #006f66; background: #dff8f4; font-size: .68rem; font-weight: 800; }
    .attention-card.is-hot .attention-pill { color: #b42318; background: #fee4e2; }
    .attention-card.is-warning .attention-pill { color: #b54708; background: #fef0c7; }
    .attention-value { color: #06346d; font-size: 1.65rem; font-weight: 800; line-height: 1; }
    .attention-card.is-hot .attention-value { color: #b42318; }
    .attention-card.is-warning .attention-value { color: #b54708; }
    .attention-text { margin: 0; color: #475467; font-size: .76rem; line-height: 1.35; }

    .dashboard-grid { display: grid; grid-template-columns: minmax(0, 1.7fr) minmax(290px, .85fr); gap: 1rem; align-items: start; }
    .main-column, .side-column { display: grid; gap: 1rem; min-width: 0; }

    .trend-body { padding: 1rem 1.1rem 1.1rem; }
    .trend-total { display: flex; align-items: baseline; justify-content: space-between; gap: 1rem; margin-bottom: 1rem; }
    .trend-total strong { color: #06346d; font-size: 1.35rem; }
    .trend-total span { color: #667085; font-size: .78rem; }
    .chart { height: 190px; display: grid; grid-template-columns: repeat(6, minmax(38px, 1fr)); gap: .7rem; align-items: end; border-bottom: 1px solid #d0d5dd; background: repeating-linear-gradient(to bottom, #fff 0, #fff 46px, #eef2f6 47px); padding: .6rem .4rem 0; }
    .bar-column { height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: .4rem; min-width: 0; }
    .bar-track { width: min(44px, 70%); height: 145px; display: flex; align-items: flex-end; }
    .bar { width: 100%; min-height: 4px; background: #008f83; border-radius: 4px 4px 0 0; }
    .bar-column.current .bar { background: #06346d; }
    .bar-label { color: #667085; font-size: .73rem; font-weight: 600; white-space: nowrap; }

    .table-wrap { overflow-x: auto; }
    .dashboard-table { width: 100%; border-collapse: collapse; min-width: 650px; }
    .dashboard-table th, .dashboard-table td { padding: .78rem 1rem; border-bottom: 1px solid #e4e7ec; text-align: left; font-size: .82rem; }
    .dashboard-table th { color: #667085; background: #f9fafb; text-transform: uppercase; font-size: .7rem; }
    .dashboard-table tr:last-child td { border-bottom: 0; }
    .dashboard-table td { color: #344054; }
    .dashboard-table strong { color: #101828; }
    .status { display: inline-flex; border-radius: 999px; padding: .2rem .5rem; font-size: .68rem; font-weight: 700; }
    .status-DRAFT { color: #175cd3; background: #eff8ff; }
    .status-SUBMITTED { color: #b54708; background: #fffaeb; }
    .status-VALIDATED { color: #027a48; background: #ecfdf3; }
    .status-REJECTED { color: #b42318; background: #fef3f2; }

    .quick-actions { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .65rem; padding: 1rem; }
    .quick-action { min-height: 76px; display: flex; flex-direction: column; justify-content: center; gap: .2rem; padding: .75rem; border: 1px solid #d0d5dd; border-radius: 8px; color: #344054; text-decoration: none; background: #fff; transition: border-color .15s ease, background .15s ease; }
    .quick-action:hover { border-color: #008f83; background: #f3fbf9; }
    .quick-action strong { color: #06346d; font-size: .83rem; }
    .quick-action span { color: #667085; font-size: .73rem; line-height: 1.3; }

    .alert-list, .suggestion-list { display: grid; gap: .65rem; padding: 1rem; }
    .alert-item, .suggestion-item { display: grid; grid-template-columns: 10px minmax(0, 1fr); gap: .7rem; padding: .85rem; border: 1px solid #e4e7ec; border-radius: 8px; color: inherit; text-decoration: none; background: #fff; }
    .alert-item:last-child, .suggestion-item:last-child { border-bottom: 0; }
    .alert-item:hover, .suggestion-item:hover { background: #f9fafb; border-color: #008f83; }
    .alert-item.warning { border-color: #fedf89; background: #fffcf5; }
    .alert-item.critical { border-color: #fecdca; background: #fff6f5; }
    .alert-item.info { border-color: #99e7dd; background: #f1fffc; }
    .signal { width: 8px; height: 8px; margin-top: .3rem; border-radius: 50%; background: #98a2b3; }
    .signal.warning { background: #f79009; }
    .signal.critical { background: #d92d20; }
    .signal.info { background: #008f83; }
    .alert-item strong, .suggestion-item strong { display: block; color: #101828; font-size: .82rem; }
    .alert-item p, .suggestion-item p { margin: .2rem 0 0; color: #667085; font-size: .76rem; line-height: 1.4; }
    .empty-state { padding: 1.25rem; color: #667085; text-align: center; font-size: .82rem; }

    @media (max-width: 1100px) {
        .overview-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .attention-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .stat:nth-child(2) { border-right: 0; }
        .stat:nth-child(-n+2) { border-bottom: 1px solid #e4e7ec; }
        .dashboard-grid { grid-template-columns: 1fr; }
    }

    @media (max-width: 640px) {
        .priority-banner { grid-template-columns: 1fr; }
        .priority-action { justify-self: stretch; }
        .overview-grid, .attention-grid { grid-template-columns: 1fr; }
        .stat, .stat:nth-child(2) { border-right: 0; border-bottom: 1px solid #e4e7ec; }
        .stat:last-child { border-bottom: 0; }
        .quick-actions { grid-template-columns: 1fr; }
        .chart { gap: .25rem; padding-inline: 0; }
        .bar-track { width: 70%; }
    }
</style>
@endpush

@section('content')
@php
    $criticalAlertCount = $alerts->where('level', 'critical')->count();
    $warningAlertCount = $alerts->where('level', 'warning')->count();
    $firstActionableAlert = $alerts->firstWhere('href');
@endphp
<div class="dashboard">
    @if($alerts->isNotEmpty())
        <section class="priority-banner {{ $criticalAlertCount > 0 ? 'critical' : '' }}" role="alert">
            <div class="priority-icon" aria-hidden="true">!</div>
            <div>
                <h2>{{ $criticalAlertCount > 0 ? 'Attention immediate requise' : 'Points a verifier' }}</h2>
                <p>
                    {{ $alerts->count() }} alerte(s) active(s), dont {{ $criticalAlertCount }} critique(s) et {{ $warningAlertCount }} ecart(s) de cotisation.
                    Les situations ci-dessous doivent etre traitees en priorite pour garder les dossiers coherents.
                </p>
            </div>
            @if($firstActionableAlert)
                <a class="priority-action" href="{{ $firstActionableAlert['href'] }}">Ouvrir le dossier</a>
            @endif
        </section>
    @endif

    <section class="dashboard-panel">
        <div class="panel-heading">
            <div>
                <h2>Vue d ensemble</h2>
                <p>Indicateurs consolides de l activite CNSS</p>
            </div>
        </div>
        <div class="overview-grid">
            <article class="stat">
                <p class="stat-label">Employeurs actifs</p>
                <p class="stat-value">{{ number_format($stats['active_employers'], 0, ',', ' ') }}</p>
                <p class="stat-note">Employeurs actuellement affilies</p>
            </article>
            <article class="stat">
                <p class="stat-label">Travailleurs actifs</p>
                <p class="stat-value">{{ number_format($stats['active_workers'], 0, ',', ' ') }}</p>
                <p class="stat-note">Rattachements actifs declares</p>
            </article>
            <article class="stat">
                <p class="stat-label">Declarations a traiter</p>
                <p class="stat-value">{{ number_format($stats['pending_declarations'], 0, ',', ' ') }}</p>
                <p class="stat-note {{ $operationalCounts['overdue_declarations'] > 0 ? 'attention' : '' }}">
                    {{ $operationalCounts['overdue_declarations'] }} en retard
                </p>
            </article>
            <article class="stat">
                <p class="stat-label">Cotisations declarees {{ now()->year }}</p>
                <p class="stat-value">{{ number_format($stats['year_contributions'], 2, ',', ' ') }}</p>
                <p class="stat-note">CDF cumules sur l annee</p>
            </article>
        </div>
        <div class="attention-grid" aria-label="Cartes d attention">
            <a class="attention-card {{ $operationalCounts['contribution_anomalies'] > 0 ? 'is-hot' : '' }}" href="{{ route('declarations.interface') }}">
                <div class="attention-top">
                    <span class="attention-label">Ecarts de cotisation</span>
                    <span class="attention-pill">{{ $operationalCounts['contribution_anomalies'] > 0 ? 'A traiter' : 'OK' }}</span>
                </div>
                <strong class="attention-value">{{ number_format($operationalCounts['contribution_anomalies'], 0, ',', ' ') }}</strong>
                <p class="attention-text">Cotisations versees differentes du total exigible.</p>
            </a>
            <a class="attention-card {{ $operationalCounts['overdue_declarations'] > 0 ? 'is-hot' : '' }}" href="{{ route('declarations.interface') }}">
                <div class="attention-top">
                    <span class="attention-label">Declarations en retard</span>
                    <span class="attention-pill">{{ $operationalCounts['overdue_declarations'] > 0 ? 'Urgent' : 'OK' }}</span>
                </div>
                <strong class="attention-value">{{ number_format($operationalCounts['overdue_declarations'], 0, ',', ' ') }}</strong>
                <p class="attention-text">Dossiers non finalises apres la date d echeance.</p>
            </a>
            @if($canManageBusiness)
                <a class="attention-card {{ $operationalCounts['pending_affiliations'] > 0 ? 'is-info' : '' }}" href="{{ route('affiliations.index') }}">
                    <div class="attention-top">
                        <span class="attention-label">Affiliations</span>
                        <span class="attention-pill">{{ $operationalCounts['pending_affiliations'] > 0 ? 'A verifier' : 'OK' }}</span>
                    </div>
                    <strong class="attention-value">{{ number_format($operationalCounts['pending_affiliations'], 0, ',', ' ') }}</strong>
                    <p class="attention-text">Demandes publiques en attente de decision.</p>
                </a>
            @else
                <div class="attention-card {{ $operationalCounts['pending_affiliations'] > 0 ? 'is-info' : '' }}">
                    <div class="attention-top">
                        <span class="attention-label">Affiliations</span>
                        <span class="attention-pill">{{ $operationalCounts['pending_affiliations'] > 0 ? 'A verifier' : 'OK' }}</span>
                    </div>
                    <strong class="attention-value">{{ number_format($operationalCounts['pending_affiliations'], 0, ',', ' ') }}</strong>
                    <p class="attention-text">Demandes publiques en attente de decision.</p>
                </div>
            @endif
            <a class="attention-card {{ $operationalCounts['missing_salaries'] > 0 ? 'is-warning' : '' }}" href="{{ route('workers.interface') }}">
                <div class="attention-top">
                    <span class="attention-label">Salaires manquants</span>
                    <span class="attention-pill">{{ $operationalCounts['missing_salaries'] > 0 ? 'Bloquant' : 'OK' }}</span>
                </div>
                <strong class="attention-value">{{ number_format($operationalCounts['missing_salaries'], 0, ',', ' ') }}</strong>
                <p class="attention-text">Rattachements actifs sans salaire de base.</p>
            </a>
        </div>
    </section>

    <div class="dashboard-grid">
        <div class="main-column">
            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Evolution des cotisations</h2>
                        <p>Montants declares sur les six derniers mois</p>
                    </div>
                </div>
                <div class="trend-body">
                    <div class="trend-total">
                        <strong>{{ number_format($trend->sum('amount'), 2, ',', ' ') }} CDF</strong>
                        <span>Total sur 6 mois</span>
                    </div>
                    <div class="chart" aria-label="Evolution mensuelle des cotisations">
                        @foreach($trend as $month)
                            @php $height = $month['amount'] > 0 ? max(5, ($month['amount'] / $trendMax) * 100) : 2; @endphp
                            <div class="bar-column {{ $loop->last ? 'current' : '' }}" title="{{ $month['period'] }} : {{ number_format($month['amount'], 2, ',', ' ') }} CDF">
                                <div class="bar-track"><div class="bar" style="height: {{ $height }}%;"></div></div>
                                <span class="bar-label">{{ $month['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-heading">
                    <div>
                        <h2>Declarations recentes</h2>
                        <p>Derniers dossiers mis a jour</p>
                    </div>
                    <a class="section-link" href="{{ route('declarations.interface') }}">Voir tout</a>
                </div>
                @if($recentDeclarations->isEmpty())
                    <div class="empty-state">Aucune declaration enregistree.</div>
                @else
                    <div class="table-wrap">
                        <table class="dashboard-table">
                            <thead><tr><th>Employeur</th><th>Periode</th><th>Statut</th><th>Cotisation</th><th></th></tr></thead>
                            <tbody>
                            @foreach($recentDeclarations as $declaration)
                                <tr>
                                    <td><strong>{{ $declaration->employer?->legal_name ?? 'Employeur' }}</strong></td>
                                    <td>{{ str_pad((string) $declaration->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $declaration->period_year }}</td>
                                    <td><span class="status status-{{ $declaration->status }}">{{ $declaration->status }}</span></td>
                                    <td>{{ number_format((float) $declaration->total_declared_contribution, 2, ',', ' ') }} CDF</td>
                                    <td><a class="section-link" href="{{ route('declarations.show', $declaration) }}">Ouvrir</a></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>

        <aside class="side-column">
            <section class="dashboard-panel">
                <div class="panel-heading"><div><h2>Actions rapides</h2><p>Acces aux taches courantes</p></div></div>
                <div class="quick-actions">
                    @if($canManageBusiness)
                        <a class="quick-action" href="{{ route('affiliations.index') }}"><strong>Traiter les affiliations</strong><span>Verifier les nouvelles demandes</span></a>
                    @endif
                    <a class="quick-action" href="{{ route('declarations.interface') }}"><strong>Gerer les declarations</strong><span>Creer, controler ou soumettre</span></a>
                    <a class="quick-action" href="{{ route('employers.interface') }}"><strong>Gerer les employeurs</strong><span>Consulter le registre</span></a>
                    <a class="quick-action" href="{{ route('workers.interface') }}"><strong>Gerer les travailleurs</strong><span>Completer les rattachements</span></a>
                    @if($isAdmin)
                        <a class="quick-action" href="{{ route('contribution-rates.index') }}"><strong>Parametres de cotisation</strong><span>Verifier les taux applicables</span></a>
                    @endif
                </div>
            </section>

            <section class="dashboard-panel">
                <div class="panel-heading"><div><h2>Alertes prioritaires</h2><p>Situations qui demandent votre attention</p></div></div>
                @if($alerts->isEmpty())
                    <div class="empty-state">Aucune alerte prioritaire.</div>
                @else
                    <div class="alert-list">
                        @foreach($alerts as $alert)
                            @if($alert['href'])<a class="alert-item" href="{{ $alert['href'] }}">@else<div class="alert-item">@endif
                                <span class="signal {{ $alert['level'] }}"></span>
                                <div><strong>{{ $alert['title'] }}</strong><p>{{ $alert['message'] }}</p></div>
                            @if($alert['href'])</a>@else</div>@endif
                        @endforeach
                    </div>
                @endif
            </section>

            <section class="dashboard-panel">
                <div class="panel-heading"><div><h2>Suggestions</h2><p>Prochaines actions recommandees</p></div></div>
                <div class="suggestion-list">
                    @foreach($suggestions as $suggestion)
                        @if($suggestion['href'])<a class="suggestion-item" href="{{ $suggestion['href'] }}">@else<div class="suggestion-item">@endif
                            <span class="signal info"></span>
                            <div><strong>{{ $suggestion['title'] }}</strong><p>{{ $suggestion['message'] }}</p></div>
                        @if($suggestion['href'])</a>@else</div>@endif
                    @endforeach
                </div>
            </section>
        </aside>
    </div>
</div>
@endsection
