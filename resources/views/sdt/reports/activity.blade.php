@extends('layouts.app')

@section('title', 'Rapport activite SDT | CNSS')
@section('page_title', 'Rapport d activite')
@section('page_subtitle', 'Lecture SDT des mouvements et volumes operationnels')

@push('styles')
<style>
    .report { display: grid; gap: 1rem; }
    .panel { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); overflow: hidden; }
    .panel-head { display: flex; justify-content: space-between; gap: 1rem; align-items: center; padding: 1rem; border-bottom: 1px solid #e4e7ec; flex-wrap: wrap; }
    .panel-head h2 { margin: 0; font-size: 1rem; color: #06346d; }
    .filters { display: flex; gap: .6rem; align-items: end; flex-wrap: wrap; }
    label { display: grid; gap: .3rem; color: #344054; font-size: .78rem; font-weight: 700; }
    input { border: 1px solid #d0d5dd; border-radius: 8px; padding: .52rem .65rem; font: inherit; }
    .btn { border: 0; border-radius: 8px; padding: .55rem .8rem; font: inherit; font-size: .82rem; font-weight: 800; cursor: pointer; background: #06346d; color: #fff; }
    .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); border: 1px solid #e4e7ec; border-radius: 8px; overflow: hidden; background: #fff; }
    .metric { padding: 1rem; border-right: 1px solid #e4e7ec; border-bottom: 1px solid #e4e7ec; min-height: 116px; }
    .metric:nth-child(4n) { border-right: 0; }
    .metric-label { margin: 0 0 .5rem; color: #667085; font-size: .78rem; font-weight: 700; }
    .metric-value { margin: 0; color: #101828; font-size: 1.6rem; font-weight: 800; }
    .metric-note { margin: .3rem 0 0; color: #667085; font-size: .76rem; }
    .attention .metric-value { color: #b54708; }
    .split { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .list { display: grid; }
    .row { display: grid; gap: .2rem; padding: .85rem 1rem; border-bottom: 1px solid #e4e7ec; }
    .row:last-child { border-bottom: 0; }
    .row strong { color: #101828; }
    .row span { color: #667085; font-size: .82rem; }
    .badge { display: inline-flex; width: fit-content; border-radius: 999px; padding: .2rem .55rem; font-size: .7rem; font-weight: 800; background: #e4f7f3; color: #006f66; }
    .badge-warn { background: #fffaeb; color: #b54708; }
    .badge-hot { background: #fef3f2; color: #b42318; }
    @media (max-width: 1000px) { .metric-grid, .split { grid-template-columns: repeat(2, minmax(0, 1fr)); } .metric:nth-child(2n) { border-right: 0; } }
    @media (max-width: 640px) { .metric-grid, .split { grid-template-columns: 1fr; } .metric { border-right: 0; } }
</style>
@endpush

@section('content')
<div class="report">
    <section class="panel">
        <div class="panel-head">
            <h2>Synthese activite</h2>
            <form class="filters" method="GET" action="{{ route('sdt.reports.activity') }}">
                <label>Du
                    <input type="date" name="from" value="{{ $filters['from'] }}">
                </label>
                <label>Au
                    <input type="date" name="to" value="{{ $filters['to'] }}">
                </label>
                <button class="btn" type="submit">Filtrer</button>
            </form>
        </div>

        <div class="metric-grid">
            <article class="metric">
                <p class="metric-label">Employeurs</p>
                <p class="metric-value">{{ number_format($summary['employers'], 0, ',', ' ') }}</p>
                <p class="metric-note">registre global</p>
            </article>
            <article class="metric">
                <p class="metric-label">Travailleurs actifs</p>
                <p class="metric-value">{{ number_format($summary['active_workers'], 0, ',', ' ') }}</p>
                <p class="metric-note">rattachements actifs</p>
            </article>
            <article class="metric attention">
                <p class="metric-label">Affiliations recues</p>
                <p class="metric-value">{{ number_format($summary['affiliations'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $summary['affiliations_pending'] }} en attente</p>
            </article>
            <article class="metric">
                <p class="metric-label">Affiliations approuvees</p>
                <p class="metric-value">{{ number_format($summary['affiliations_approved'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $summary['affiliations_rejected'] }} rejetee(s)</p>
            </article>
            <article class="metric {{ $summary['sdt_opinions_waiting'] > 0 ? 'attention' : '' }}">
                <p class="metric-label">Avis SDT demandes</p>
                <p class="metric-value">{{ number_format($summary['sdt_opinions_requested'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $summary['sdt_opinions_waiting'] }} en attente de reponse</p>
            </article>
            <article class="metric">
                <p class="metric-label">Declarations creees</p>
                <p class="metric-value">{{ number_format($summary['declarations'], 0, ',', ' ') }}</p>
                <p class="metric-note">sur la periode filtree</p>
            </article>
            <article class="metric attention">
                <p class="metric-label">File active</p>
                <p class="metric-value">{{ number_format($summary['declarations_draft'] + $summary['declarations_submitted'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $summary['declarations_draft'] }} brouillon(s), {{ $summary['declarations_submitted'] }} soumis</p>
            </article>
            <article class="metric">
                <p class="metric-label">Decisions declarations</p>
                <p class="metric-value">{{ number_format($summary['declarations_validated'] + $summary['declarations_rejected'], 0, ',', ' ') }}</p>
                <p class="metric-note">{{ $summary['declarations_validated'] }} validee(s), {{ $summary['declarations_rejected'] }} rejetee(s)</p>
            </article>
        </div>
    </section>

    <div class="split">
        <section class="panel">
            <div class="panel-head">
                <h2>Dernieres affiliations</h2>
            </div>
            <div class="list">
                @forelse($recentAffiliations as $request)
                    <div class="row">
                        <strong>{{ $request->legal_name ?: ($request->physical_employer_name ?: 'Employeur') }}</strong>
                        <span>{{ $request->tracking_number }} - {{ $request->created_at?->format('Y-m-d H:i') }}</span>
                        <span class="badge {{ $request->status === 'REJECTED' ? 'badge-hot' : ($request->status === 'PENDING' ? 'badge-warn' : '') }}">{{ $request->status }}</span>
                    </div>
                @empty
                    <div class="row"><span>Aucune affiliation dans la periode.</span></div>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <div class="panel-head">
                <h2>Dernieres declarations</h2>
            </div>
            <div class="list">
                @forelse($recentDeclarations as $declaration)
                    <div class="row">
                        <strong>{{ $declaration->employer?->legal_name ?? 'Employeur' }}</strong>
                        <span>Declaration {{ str_pad((string) $declaration->period_month, 2, '0', STR_PAD_LEFT) }}/{{ $declaration->period_year }} - {{ $declaration->created_at?->format('Y-m-d H:i') }}</span>
                        <span class="badge {{ in_array($declaration->status, ['DRAFT', 'SUBMITTED'], true) ? 'badge-warn' : ($declaration->status === 'REJECTED' ? 'badge-hot' : '') }}">{{ $declaration->status }}</span>
                    </div>
                @empty
                    <div class="row"><span>Aucune declaration dans la periode.</span></div>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
