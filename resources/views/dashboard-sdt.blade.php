@extends('layouts.app')

@section('title', 'Tableau de bord SDT | CNSS')
@section('page_title', 'Tableau de bord SDT')
@section('page_subtitle', 'Etat complet du systeme au '.now()->format('d/m/Y'))

@push('styles')
<style>
    .sdt-dashboard { display: grid; gap: 1rem; }
    .notification-hero { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 1rem; align-items: center; padding: 1rem 1.1rem; border: 1px solid #99e7dd; border-left: 6px solid #008f83; border-radius: 8px; background: linear-gradient(90deg, #f1fffc 0%, #fff 72%); box-shadow: 0 8px 22px rgba(6, 52, 109, .08); }
    .notification-hero h2 { margin: 0; color: #06346d; font-size: 1.05rem; }
    .notification-hero p { margin: .25rem 0 0; color: #475467; font-size: .84rem; line-height: 1.45; }
    .notification-count { min-width: 86px; display: grid; place-items: center; padding: .75rem; border-radius: 8px; background: #06346d; color: #fff; text-align: center; }
    .notification-count strong { display: block; font-size: 1.7rem; line-height: 1; }
    .notification-count span { display: block; margin-top: .25rem; font-size: .68rem; font-weight: 800; text-transform: uppercase; }
    .panel { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); overflow: hidden; }
    .panel-head { padding: 1rem; border-bottom: 1px solid #e4e7ec; }
    .panel-head h2 { margin: 0; color: #101828; font-size: 1rem; }
    .panel-head p { margin: .25rem 0 0; color: #667085; font-size: .82rem; }
    .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); background: #fbfcfe; gap: .75rem; padding: 1rem; }
    .metric { padding: 1rem; border: 1px solid #e4e7ec; border-radius: 8px; min-width: 0; background: #fff; position: relative; overflow: hidden; }
    .metric::before { content: ""; position: absolute; inset: 0 auto 0 0; width: 4px; background: #99e7dd; }
    .metric.warning::before { background: #f79009; }
    .metric.critical::before { background: #d92d20; }
    .metric:nth-child(4n) { border-right: 0; }
    .metric-label { margin: 0; color: #667085; font-size: .76rem; font-weight: 700; text-transform: uppercase; }
    .metric-value { margin: .45rem 0 .2rem; color: #06346d; font-size: clamp(1.45rem, 2.4vw, 2rem); font-weight: 800; line-height: 1; }
    .metric-note { margin: 0; color: #667085; font-size: .76rem; }
    .metric.warning .metric-value, .metric.warning .metric-note { color: #b54708; }
    .metric.critical .metric-value, .metric.critical .metric-note { color: #b42318; }
    .main-grid { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(330px, .95fr); gap: 1rem; align-items: start; }
    .chart { height: 190px; display: grid; grid-template-columns: repeat(6, minmax(38px, 1fr)); gap: .7rem; align-items: end; border-bottom: 1px solid #d0d5dd; background: repeating-linear-gradient(to bottom, #fff 0, #fff 46px, #eef2f6 47px); padding: .8rem .8rem 0; }
    .bar-column { height: 100%; display: flex; flex-direction: column; justify-content: flex-end; align-items: center; gap: .4rem; min-width: 0; }
    .bar-track { width: min(44px, 70%); height: 145px; display: flex; align-items: flex-end; }
    .bar { width: 100%; min-height: 4px; background: #008f83; border-radius: 4px 4px 0 0; }
    .bar-column.current .bar { background: #06346d; }
    .bar-label { color: #667085; font-size: .73rem; font-weight: 700; white-space: nowrap; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; min-width: 650px; border-collapse: collapse; }
    th, td { padding: .78rem 1rem; border-bottom: 1px solid #e4e7ec; text-align: left; font-size: .82rem; }
    th { color: #667085; background: #f9fafb; text-transform: uppercase; font-size: .7rem; }
    td strong { color: #101828; }
    .status { display: inline-flex; border-radius: 999px; padding: .2rem .5rem; font-size: .68rem; font-weight: 800; }
    .status-DRAFT { color: #175cd3; background: #eff8ff; }
    .status-SUBMITTED { color: #b54708; background: #fffaeb; }
    .status-VALIDATED { color: #027a48; background: #ecfdf3; }
    .status-REJECTED { color: #b42318; background: #fef3f2; }
    .alert-list { display: grid; gap: .7rem; padding: 1rem; }
    .alert-item { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: .75rem; padding: .9rem; border: 1px solid #e4e7ec; border-radius: 8px; background: #fff; }
    .alert-item.warning { border-color: #fedf89; background: #fffcf5; }
    .alert-item.critical { border-color: #fecdca; background: #fff6f5; }
    .alert-item.info { border-color: #99e7dd; background: #f1fffc; }
    .signal { width: 34px; height: 34px; display: grid; place-items: center; margin-top: 0; border-radius: 50%; background: #f2f4f7; color: #667085; font-weight: 900; }
    .signal.warning { background: #fef0c7; color: #b54708; }
    .signal.critical { background: #fee4e2; color: #b42318; }
    .signal.info { background: #dff8f4; color: #006f66; }
    .alert-item strong { display: block; color: #101828; font-size: .82rem; }
    .alert-item p { margin: .2rem 0 0; color: #667085; font-size: .76rem; line-height: 1.4; }
    .timeline { display: grid; gap: .75rem; padding: 1rem; }
    .timeline-item { display: grid; grid-template-columns: auto minmax(0, 1fr); gap: .75rem; align-items: start; padding: .85rem; border: 1px solid #e4e7ec; border-radius: 8px; background: #fff; }
    .timeline-dot { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 50%; background: #e7f0fa; color: #06346d; font-weight: 900; }
    .timeline-item strong { display: block; color: #101828; font-size: .84rem; }
    .timeline-item p { margin: .22rem 0 0; color: #667085; font-size: .76rem; line-height: 1.4; }
    .timeline-meta { margin-top: .4rem; display: flex; flex-wrap: wrap; gap: .35rem; }
    .mini-badge { display: inline-flex; border-radius: 999px; padding: .2rem .48rem; background: #f2f4f7; color: #475467; font-size: .68rem; font-weight: 800; }
    .empty { padding: 1.2rem; color: #667085; text-align: center; font-size: .82rem; }
    @media (max-width: 1000px) {
        .metric-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .metric:nth-child(2n) { border-right: 0; }
        .main-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .notification-hero { grid-template-columns: 1fr; }
        .notification-count { place-items: start; text-align: left; }
        .metric-grid { grid-template-columns: 1fr; }
        .metric { border-right: 0; }
        .chart { gap: .25rem; padding-inline: .25rem; }
    }
</style>
@endpush

@section('content')
@php
    $notificationTotal = $alerts->count()
        + $operationalCounts['pending_affiliations']
        + $operationalCounts['contribution_anomalies']
        + $operationalCounts['overdue_declarations']
        + $operationalCounts['missing_salaries'];
@endphp
<div class="sdt-dashboard">
    <section class="notification-hero">
        <div>
            <h2>Centre de notifications operationnelles</h2>
            <p>Le SDT dispose ici d'une lecture globale des evenements du systeme: demandes en attente, ecarts de cotisation, retards, salaires manquants et alertes de controle.</p>
        </div>
        <div class="notification-count">
            <strong>{{ number_format($notificationTotal, 0, ',', ' ') }}</strong>
            <span>signaux</span>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Notifications par domaine</h2>
            <p>Vue rapide des indicateurs qui demandent une surveillance.</p>
        </div>
        <div class="metric-grid">
            <article class="metric">
                <p class="metric-label">Registre employeurs</p>
                <p class="metric-value">{{ number_format($stats['active_employers'], 0, ',', ' ') }}</p>
                <p class="metric-note">Employeurs affilies</p>
            </article>
            <article class="metric">
                <p class="metric-label">Registre travailleurs</p>
                <p class="metric-value">{{ number_format($stats['active_workers'], 0, ',', ' ') }}</p>
                <p class="metric-note">Rattachements declares actifs</p>
            </article>
            <article class="metric {{ $operationalCounts['overdue_declarations'] > 0 ? 'critical' : '' }}">
                <p class="metric-label">File declarations</p>
                <p class="metric-value">{{ number_format($stats['pending_declarations'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $operationalCounts['overdue_declarations'] }} en retard</p>
            </article>
            <article class="metric">
                <p class="metric-label">Cotisations {{ now()->year }}</p>
                <p class="metric-value">{{ number_format($stats['year_contributions'], 2, ',', ' ') }}</p>
                <p class="metric-note">CDF cumules</p>
            </article>
            <article class="metric {{ $operationalCounts['pending_affiliations'] > 0 ? 'warning' : '' }}">
                <p class="metric-label">Notification affiliation</p>
                <p class="metric-value">{{ number_format($operationalCounts['pending_affiliations'], 0, ',', ' ') }}</p>
                <p class="metric-note">Demandes non traitees</p>
            </article>
            <article class="metric {{ $operationalCounts['contribution_anomalies'] > 0 ? 'critical' : '' }}">
                <p class="metric-label">Notification cotisation</p>
                <p class="metric-value">{{ number_format($operationalCounts['contribution_anomalies'], 0, ',', ' ') }}</p>
                <p class="metric-note">Montant cotise different du total exigible</p>
            </article>
            <article class="metric {{ $operationalCounts['missing_salaries'] > 0 ? 'warning' : '' }}">
                <p class="metric-label">Notification salaire</p>
                <p class="metric-value">{{ number_format($operationalCounts['missing_salaries'], 0, ',', ' ') }}</p>
                <p class="metric-note">Rattachements incomplets</p>
            </article>
            <article class="metric {{ $operationalCounts['open_fraud_alerts'] > 0 ? 'critical' : '' }}">
                <p class="metric-label">Notification controle</p>
                <p class="metric-value">{{ number_format($operationalCounts['open_fraud_alerts'], 0, ',', ' ') }}</p>
                <p class="metric-note">Alertes ouvertes</p>
            </article>
        </div>
    </section>

    <div class="main-grid">
        <section class="panel">
            <div class="panel-head">
                <h2>Evolution des cotisations</h2>
                <p>Montants declares sur les six derniers mois.</p>
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
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Flux de notifications</h2>
                <p>Evenements prioritaires visibles sans action directe SDT.</p>
            </div>
            @if($alerts->isEmpty())
                <div class="empty">Aucune alerte prioritaire.</div>
            @else
                <div class="alert-list">
                    @foreach($alerts as $alert)
                        <div class="alert-item {{ $alert['level'] }}">
                            <span class="signal {{ $alert['level'] }}">!</span>
                            <div><strong>{{ $alert['title'] }}</strong><p>{{ $alert['message'] }}</p></div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>

    <section class="panel">
        <div class="panel-head">
            <h2>Journal recent</h2>
            <p>Derniers mouvements observes sur les declarations.</p>
        </div>
        @if($recentDeclarations->isEmpty())
            <div class="empty">Aucune declaration enregistree.</div>
        @else
            <div class="timeline">
                @foreach($recentDeclarations as $declaration)
                    <article class="timeline-item">
                        <span class="timeline-dot">D</span>
                        <div>
                            <strong>{{ $declaration->employer?->legal_name ?? 'Employeur' }}</strong>
                            <p>Declaration {{ str_pad((string) $declaration->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $declaration->period_year }} mise a jour.</p>
                            <div class="timeline-meta">
                                <span class="status status-{{ $declaration->status }}">{{ $declaration->status }}</span>
                                <span class="mini-badge">{{ number_format((float) $declaration->total_declared_contribution, 2, ',', ' ') }} CDF</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
