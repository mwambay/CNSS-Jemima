@extends('layouts.app')

@section('title', 'Demande affiliation | CNSS')
@section('page_title', 'Demande '.$affiliationRequest->tracking_number)
@section('page_subtitle', 'Controle et decision')

@push('styles')
<style>
    .page { display: grid; gap: 1rem; }
    .panel { background: #fff; border: 1px solid #e7eef2; border-radius: 16px; padding: 1rem; box-shadow: 0 1px 3px rgba(6, 52, 109, .1); }
    .toolbar { display: flex; justify-content: space-between; align-items: center; gap: .75rem; flex-wrap: wrap; margin-bottom: .8rem; }
    .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .75rem; }
    .item { border: 1px solid #e4e7ec; border-radius: 12px; padding: .7rem .8rem; background: #fcfcfd; }
    .item.full { grid-column: 1 / -1; }
    .label { margin: 0 0 .35rem; color: #667085; font-size: .77rem; text-transform: uppercase; font-weight: 700; }
    .value { margin: 0; color: #101828; font-size: .9rem; font-weight: 600; word-break: break-word; }
    .btn, button { border: 0; border-radius: 10px; padding: .56rem .86rem; font: inherit; font-size: .84rem; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }
    .btn-outline { background: #fff; color: #344054; border: 1px solid #d0d5dd; }
    .btn-primary { background: #06346d; color: #fff; }
    .btn-danger { background: #e4f7f3; color: #006f66; border: 1px solid #8acdc4; }
    .forms { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .field { display: grid; gap: .35rem; margin-bottom: .7rem; }
    label { color: #344054; font-size: .82rem; font-weight: 700; }
    input, textarea { width: 100%; border: 1px solid #d0d5dd; border-radius: 10px; padding: .6rem .72rem; font: inherit; }
    textarea { min-height: 88px; resize: vertical; }
    .notice { border: 1px solid #abefc6; background: #ecfdf3; color: #027a48; border-radius: 12px; padding: .75rem; font-weight: 700; }
    .warning { border: 1px solid #fedf89; background: #fffaeb; color: #93370d; border-radius: 12px; padding: .75rem; font-weight: 700; }
    .opinion { display: grid; gap: .75rem; border-left: 4px solid #008f83; }
    .opinion-head { display: flex; justify-content: space-between; gap: .75rem; flex-wrap: wrap; align-items: center; }
    .opinion-head h2 { margin: 0; font-size: 1.05rem; color: #06346d; }
    .opinion-status { display: inline-flex; border-radius: 999px; padding: .24rem .62rem; font-size: .74rem; font-weight: 800; background: #e4f7f3; color: #006f66; }
    .opinion-status.requested { background: #fffaeb; color: #b54708; }
    .opinion-status.unfavorable { background: #fef3f2; color: #b42318; }
    .error { color: #b42318; font-size: .82rem; }
    select { width: 100%; border: 1px solid #d0d5dd; border-radius: 10px; padding: .6rem .72rem; font: inherit; background: #fff; }
    @media (max-width: 900px) { .grid, .forms { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
@php
    $sdtMode = $sdtMode ?? false;
    $sdtOpinionRequested = (bool) $affiliationRequest->sdt_opinion_requested_at;
    $sdtOpinionGiven = (bool) $affiliationRequest->sdt_opinion_given_at;
    $isWaitingForSdt = $sdtOpinionRequested && ! $sdtOpinionGiven;
    $sdtOpinionLabel = match ($affiliationRequest->sdt_opinion_status) {
        'REQUESTED' => 'Avis demandé',
        'FAVORABLE' => 'Avis favorable',
        'UNFAVORABLE' => 'Avis défavorable',
        default => 'Non demandé',
    };
    $statusLabel = match ($affiliationRequest->status) {
        'APPROVED' => 'Approuvée',
        'REJECTED' => 'Rejetée',
        default => 'En attente',
    };
@endphp
<div class="page">
    @if(session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    @if($isWaitingForSdt && ! $sdtMode)
        <div class="warning">Decision suspendue: le SDT doit transmettre son avis avant approbation ou rejet.</div>
    @endif

    <article class="panel">
        <div class="toolbar">
            <strong>Statut: {{ $statusLabel }}</strong>
            <a class="btn btn-outline" href="{{ $sdtMode ? route('sdt.affiliations.index') : route('affiliations.index') }}">Retour</a>
        </div>
        <div class="grid">
            @foreach([
                'tracking_number' => 'Numero suivi',
                'legal_name' => 'Raison sociale',
                'physical_employer_name' => 'Personne physique',
                'legal_form' => 'Forme juridique',
                'rccm_number' => 'RCCM',
                'primary_activity' => 'Activite principale',
                'phone' => 'Telephone',
                'email' => 'Email',
                'workers_count' => 'Nombre travailleurs',
                'monthly_contribution_base_total' => 'Base mensuelle',
            ] as $name => $label)
                <div class="item">
                    <p class="label">{{ $label }}</p>
                    <p class="value">
                        @if($name === 'monthly_contribution_base_total' && $affiliationRequest->{$name} !== null)
                            {{ number_format((float) $affiliationRequest->{$name}, 2, ',', ' ') }} CDF
                        @else
                            {{ $affiliationRequest->{$name} ?? '-' }}
                        @endif
                    </p>
                </div>
            @endforeach
            <div class="item full">
                <p class="label">Adresse</p>
                <p class="value">{{ collect([$affiliationRequest->street, $affiliationRequest->district, $affiliationRequest->municipality, $affiliationRequest->city, $affiliationRequest->province])->filter()->implode(', ') ?: '-' }}</p>
            </div>
            @if($affiliationRequest->employer)
                <div class="item full">
                    <p class="label">Employeur créé</p>
                    <p class="value"><a href="{{ route('employers.show', $affiliationRequest->employer) }}">{{ $affiliationRequest->employer->affiliation_number }} - {{ $affiliationRequest->employer->legal_name }}</a></p>
                </div>
            @endif
            @if($affiliationRequest->rejection_reason)
                <div class="item full">
                    <p class="label">Motif rejet</p>
                    <p class="value">{{ $affiliationRequest->rejection_reason }}</p>
                </div>
            @endif
        </div>
    </article>

    <article class="panel opinion">
        <div class="opinion-head">
            <h2>Avis SDT</h2>
            <span class="opinion-status {{ strtolower((string) $affiliationRequest->sdt_opinion_status) }}">{{ $sdtOpinionLabel }}</span>
        </div>

        @if(! $sdtOpinionRequested)
            <p class="value">Aucun avis SDT n'a encore été demandé pour cette affiliation.</p>
            @if(! $sdtMode && $affiliationRequest->status === 'PENDING')
                <form method="POST" action="{{ route('affiliations.request-sdt-opinion', $affiliationRequest) }}">
                    @csrf
                    <button class="btn-primary" type="submit">Demander avis SDT</button>
                </form>
            @endif
        @else
            <div class="grid">
                <div class="item">
                    <p class="label">Demande par</p>
                    <p class="value">{{ $affiliationRequest->sdtOpinionRequestedBy?->full_name ?? '-' }}</p>
                </div>
                <div class="item">
                    <p class="label">Date demande</p>
                    <p class="value">{{ $affiliationRequest->sdt_opinion_requested_at?->format('Y-m-d H:i') ?? '-' }}</p>
                </div>
                <div class="item">
                    <p class="label">Réponse par</p>
                    <p class="value">{{ $affiliationRequest->sdtOpinionGivenBy?->full_name ?? '-' }}</p>
                </div>
                @if($affiliationRequest->sdt_opinion_note)
                    <div class="item full">
                        <p class="label">Avis détaillé</p>
                        <p class="value">{{ $affiliationRequest->sdt_opinion_note }}</p>
                    </div>
                @endif
            </div>
        @endif

        @if($sdtMode && $isWaitingForSdt && $affiliationRequest->status === 'PENDING')
            <form method="POST" action="{{ route('sdt.affiliations.opinion', $affiliationRequest) }}">
                @csrf
                <div class="field">
                    <label for="sdt_opinion_status">Position SDT</label>
                    <select id="sdt_opinion_status" name="sdt_opinion_status" required>
                        <option value="">Choisir un avis</option>
                        <option value="FAVORABLE" @selected(old('sdt_opinion_status') === 'FAVORABLE')>Favorable</option>
                        <option value="UNFAVORABLE" @selected(old('sdt_opinion_status') === 'UNFAVORABLE')>Défavorable</option>
                    </select>
                    @error('sdt_opinion_status') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="field">
                    <label for="sdt_opinion_note">Avis détaillé</label>
                    <textarea id="sdt_opinion_note" name="sdt_opinion_note" required>{{ old('sdt_opinion_note') }}</textarea>
                    @error('sdt_opinion_note') <span class="error">{{ $message }}</span> @enderror
                </div>
                <button class="btn-primary" type="submit">Transmettre l'avis</button>
            </form>
        @endif
    </article>

    @if(! $sdtMode && $affiliationRequest->status === 'PENDING' && ! $isWaitingForSdt)
        <section class="forms">
            <article class="panel">
                <h2>Approuver</h2>
                <form method="POST" action="{{ route('affiliations.approve', $affiliationRequest) }}">
                    @csrf
                    <div class="field">
                        <label for="affiliation_number">Numéro d'affiliation CNSS</label>
                        <input id="affiliation_number" name="affiliation_number" value="{{ old('affiliation_number') }}" required maxlength="30">
                        @error('affiliation_number') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button class="btn-primary" type="submit">Approuver et créer l'employeur</button>
                </form>
            </article>
            <article class="panel">
                <h2>Rejeter</h2>
                <form method="POST" action="{{ route('affiliations.reject', $affiliationRequest) }}">
                    @csrf
                    <div class="field">
                        <label for="rejection_reason">Motif</label>
                        <textarea id="rejection_reason" name="rejection_reason" required>{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button class="btn-danger" type="submit">Rejeter</button>
                </form>
            </article>
        </section>
    @endif
</div>
@endsection
