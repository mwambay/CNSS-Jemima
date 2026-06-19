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
    .btn-danger { background: #f04438; color: #fff; }
    .forms { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
    .field { display: grid; gap: .35rem; margin-bottom: .7rem; }
    label { color: #344054; font-size: .82rem; font-weight: 700; }
    input, textarea { width: 100%; border: 1px solid #d0d5dd; border-radius: 10px; padding: .6rem .72rem; font: inherit; }
    textarea { min-height: 88px; resize: vertical; }
    .notice { border: 1px solid #abefc6; background: #ecfdf3; color: #027a48; border-radius: 12px; padding: .75rem; font-weight: 700; }
    .error { color: #b42318; font-size: .82rem; }
    @media (max-width: 900px) { .grid, .forms { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page">
    @if(session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    <article class="panel">
        <div class="toolbar">
            <strong>Statut: {{ $affiliationRequest->status }}</strong>
            <a class="btn btn-outline" href="{{ route('affiliations.index') }}">Retour</a>
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
                    <p class="value">{{ $affiliationRequest->{$name} ?? '-' }}</p>
                </div>
            @endforeach
            <div class="item full">
                <p class="label">Adresse</p>
                <p class="value">{{ collect([$affiliationRequest->street, $affiliationRequest->district, $affiliationRequest->municipality, $affiliationRequest->city, $affiliationRequest->province])->filter()->implode(', ') ?: '-' }}</p>
            </div>
            @if($affiliationRequest->employer)
                <div class="item full">
                    <p class="label">Employeur cree</p>
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

    @if($affiliationRequest->status === 'PENDING')
        <section class="forms">
            <article class="panel">
                <h2>Approuver</h2>
                <form method="POST" action="{{ route('affiliations.approve', $affiliationRequest) }}">
                    @csrf
                    <div class="field">
                        <label for="affiliation_number">Numero d'affiliation CNSS</label>
                        <input id="affiliation_number" name="affiliation_number" value="{{ old('affiliation_number') }}" required maxlength="30">
                        @error('affiliation_number') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <button class="btn-primary" type="submit">Approuver et creer employeur</button>
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
