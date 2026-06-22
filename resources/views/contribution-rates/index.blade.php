@extends('layouts.app')

@section('title', 'Parametres cotisations | CNSS')
@section('page_title', 'Parametres de cotisation')
@section('page_subtitle', 'Taux, periodes et bornes de calcul')

@push('styles')
<style>
    .settings-page { display: grid; gap: 1rem; }
    .panel { background: #fff; border: 1px solid #e7eef2; border-radius: 8px; padding: 1rem; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); }
    .panel-head { display: flex; justify-content: space-between; align-items: center; gap: .7rem; margin-bottom: .9rem; flex-wrap: wrap; }
    .panel h2 { margin: 0; color: #06346d; font-size: 1.02rem; }
    .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem; }
    .field { display: grid; gap: .35rem; }
    .field label { color: #344054; font-size: .82rem; font-weight: 700; }
    .control { width: 100%; min-height: 42px; border: 1px solid #d0d5dd; border-radius: 8px; padding: .55rem .7rem; font: inherit; }
    .control:focus { outline: 0; border-color: #008f83; box-shadow: 0 0 0 4px rgba(0, 143, 131, .13); }
    .switch-field { display: flex; align-items: center; gap: .5rem; min-height: 42px; padding-top: 1.25rem; }
    .actions { display: flex; justify-content: flex-end; gap: .55rem; margin-top: 1rem; }
    .btn { border-radius: 8px; padding: .62rem .85rem; border: 1px solid transparent; font: inherit; font-size: .84rem; font-weight: 700; cursor: pointer; }
    .btn-primary { background: #06346d; color: #fff; }
    .btn-outline { background: #fff; color: #344054; border-color: #d0d5dd; }
    .notice { border: 1px solid #a7e0d7; background: #e4f7f3; color: #006f66; border-radius: 8px; padding: .7rem .8rem; font-weight: 700; }
    .errors { border: 1px solid #fecdca; background: #fef3f2; color: #b42318; border-radius: 8px; padding: .7rem .8rem; }
    .errors ul { margin: 0; padding-left: 1.15rem; }
    .table-wrap { overflow: auto; border: 1px solid #e7eef2; border-radius: 8px; }
    table { width: 100%; min-width: 980px; border-collapse: collapse; }
    th, td { padding: .68rem .72rem; border-bottom: 1px solid #e7eef2; text-align: left; font-size: .86rem; }
    th { color: #667085; background: #f8fafb; font-size: .75rem; text-transform: uppercase; }
    .badge { display: inline-flex; border-radius: 999px; padding: .2rem .55rem; font-size: .74rem; font-weight: 800; }
    .badge-active { background: #e4f7f3; color: #006f66; }
    .badge-inactive { background: #f2f4f7; color: #667085; }
    .empty { text-align: center; color: #667085; padding: 1rem; }
    .rate-dialog { width: min(860px, calc(100% - 2rem)); max-height: calc(100vh - 2rem); border: 0; border-radius: 8px; padding: 0; color: #101828; box-shadow: 0 24px 60px rgba(6, 52, 109, .24); }
    .rate-dialog::backdrop { background: rgba(6, 32, 66, .58); }
    .modal-shell { padding: 1rem; }
    .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding-bottom: .8rem; margin-bottom: .9rem; border-bottom: 1px solid #e7eef2; }
    .modal-head h2 { margin: 0; color: #06346d; font-size: 1.05rem; }
    .modal-errors { margin-bottom: .85rem; }
    @media (max-width: 650px) { .form-grid { grid-template-columns: 1fr; } .switch-field { padding-top: 0; } }
</style>
@endpush

@section('content')
<div class="settings-page">
    @if(session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    <section class="panel">
        <div class="panel-head">
            <h2>Historique des modalites</h2>
            <button class="btn btn-primary" id="add-rate" type="button">Ajouter une modalite</button>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Regime</th>
                    <th>Periode</th>
                    <th>Employeur</th>
                    <th>Travailleur</th>
                    <th>Total</th>
                    <th>Majoration / jour</th>
                    <th>Plancher</th>
                    <th>Plafond</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rates as $rate)
                    <tr>
                        <td>{{ $rate->regime_code }}</td>
                        <td>{{ $rate->effective_from?->format('Y-m-d') }} au {{ $rate->effective_to?->format('Y-m-d') ?? 'sans fin' }}</td>
                        <td>{{ number_format((float) $rate->employer_rate, 2, ',', ' ') }} %</td>
                        <td>{{ number_format((float) $rate->worker_rate, 2, ',', ' ') }} %</td>
                        <td><strong>{{ number_format((float) $rate->employer_rate + (float) $rate->worker_rate, 2, ',', ' ') }} %</strong></td>
                        <td>{{ number_format((float) $rate->late_penalty_daily_rate, 2, ',', ' ') }} %</td>
                        <td>{{ $rate->floor_amount !== null ? number_format((float) $rate->floor_amount, 2, ',', ' ').' CDF' : '-' }}</td>
                        <td>{{ $rate->ceiling_amount !== null ? number_format((float) $rate->ceiling_amount, 2, ',', ' ').' CDF' : '-' }}</td>
                        <td><span class="badge {{ $rate->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $rate->is_active ? 'ACTIVE' : 'INACTIVE' }}</span></td>
                        <td>
                            <button
                                class="btn btn-outline edit-rate"
                                type="button"
                                data-action="{{ route('contribution-rates.update', $rate) }}"
                                data-rate-id="{{ $rate->id }}"
                                data-regime-code="{{ $rate->regime_code }}"
                                data-effective-from="{{ $rate->effective_from?->format('Y-m-d') }}"
                                data-effective-to="{{ $rate->effective_to?->format('Y-m-d') }}"
                                data-employer-rate="{{ $rate->employer_rate }}"
                                data-worker-rate="{{ $rate->worker_rate }}"
                                data-late-penalty-daily-rate="{{ $rate->late_penalty_daily_rate }}"
                                data-floor-amount="{{ $rate->floor_amount }}"
                                data-ceiling-amount="{{ $rate->ceiling_amount }}"
                                data-is-active="{{ $rate->is_active ? '1' : '0' }}"
                            >Modifier</button>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty" colspan="10">Aucune modalite configuree.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>

@php
    $oldRate = old('rate_id') ? $rates->firstWhere('id', (int) old('rate_id')) : null;
    $isOldUpdate = old('_method') === 'PUT' && $oldRate !== null;
@endphp

<dialog class="rate-dialog" id="rate-dialog">
    <div class="modal-shell">
        <div class="modal-head">
            <h2 id="form-title">{{ $isOldUpdate ? 'Modifier '.$oldRate->regime_code : 'Nouvelle modalite' }}</h2>
            <button class="btn btn-outline" id="close-rate-dialog" type="button">Fermer</button>
        </div>

        @if($errors->any())
            <div class="errors modal-errors">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form
            id="rate-form"
            method="POST"
            action="{{ $isOldUpdate ? route('contribution-rates.update', $oldRate) : route('contribution-rates.store') }}"
            data-store-action="{{ route('contribution-rates.store') }}"
        >
            @csrf
            <input id="method-field" type="hidden" name="_method" value="{{ $isOldUpdate ? 'PUT' : 'POST' }}">
            <input id="rate-id" type="hidden" name="rate_id" value="{{ old('rate_id') }}">
            <div class="form-grid">
                <div class="field">
                    <label for="regime_code">Regime</label>
                    <input class="control" id="regime_code" name="regime_code" value="{{ old('regime_code', 'GENERAL') }}" required maxlength="50">
                </div>
                <div class="field">
                    <label for="effective_from">Valable a partir du</label>
                    <input class="control" id="effective_from" name="effective_from" type="date" value="{{ old('effective_from') }}" required>
                </div>
                <div class="field">
                    <label for="effective_to">Valable jusqu'au</label>
                    <input class="control" id="effective_to" name="effective_to" type="date" value="{{ old('effective_to') }}">
                </div>
                <div class="field">
                    <label for="employer_rate">Taux employeur (%)</label>
                    <input class="control" id="employer_rate" name="employer_rate" type="number" min="0" max="100" step="0.0001" value="{{ old('employer_rate') }}" required>
                </div>
                <div class="field">
                    <label for="worker_rate">Taux travailleur (%)</label>
                    <input class="control" id="worker_rate" name="worker_rate" type="number" min="0" max="100" step="0.0001" value="{{ old('worker_rate') }}" required>
                </div>
                <div class="field">
                    <label for="late_penalty_daily_rate">Majoration de retard par jour (%)</label>
                    <input class="control" id="late_penalty_daily_rate" name="late_penalty_daily_rate" type="number" min="0" max="100" step="0.0001" value="{{ old('late_penalty_daily_rate', 0.5) }}" required>
                </div>
                <div class="field">
                    <label for="floor_amount">Plancher cotisable (CDF)</label>
                    <input class="control" id="floor_amount" name="floor_amount" type="number" min="0" step="0.01" value="{{ old('floor_amount') }}">
                </div>
                <div class="field">
                    <label for="ceiling_amount">Plafond cotisable (CDF)</label>
                    <input class="control" id="ceiling_amount" name="ceiling_amount" type="number" min="0" step="0.01" value="{{ old('ceiling_amount') }}">
                </div>
                <label class="switch-field">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))>
                    Modalite active
                </label>
            </div>
            <div class="actions">
                <button class="btn btn-outline" id="cancel-rate" type="button">Annuler</button>
                <button class="btn btn-primary" id="submit-button" type="submit">{{ $isOldUpdate ? 'Enregistrer les modifications' : 'Creer la modalite' }}</button>
            </div>
        </form>
    </div>
</dialog>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('rate-form');
    const dialog = document.getElementById('rate-dialog');
    const methodField = document.getElementById('method-field');
    const rateIdField = document.getElementById('rate-id');
    const formTitle = document.getElementById('form-title');
    const submitButton = document.getElementById('submit-button');
    const errorPanel = document.querySelector('.modal-errors');
    const fieldDatasetKeys = {
        regime_code: 'regimeCode',
        effective_from: 'effectiveFrom',
        effective_to: 'effectiveTo',
        employer_rate: 'employerRate',
        worker_rate: 'workerRate',
        late_penalty_daily_rate: 'latePenaltyDailyRate',
        floor_amount: 'floorAmount',
        ceiling_amount: 'ceilingAmount',
    };

    function resetForm() {
        form.reset();
        form.action = form.dataset.storeAction;
        methodField.value = 'POST';
        rateIdField.value = '';
        document.getElementById('regime_code').value = 'GENERAL';
        document.getElementById('late_penalty_daily_rate').value = '0.5';
        document.getElementById('is_active').checked = true;
        formTitle.textContent = 'Nouvelle modalite';
        submitButton.textContent = 'Creer la modalite';
        if (errorPanel) {
            errorPanel.hidden = true;
        }
    }

    document.getElementById('add-rate').addEventListener('click', () => {
        resetForm();
        dialog.showModal();
    });

    document.getElementById('close-rate-dialog').addEventListener('click', () => dialog.close());
    document.getElementById('cancel-rate').addEventListener('click', () => dialog.close());

    document.querySelectorAll('.edit-rate').forEach((button) => {
        button.addEventListener('click', () => {
            form.action = button.dataset.action;
            methodField.value = 'PUT';
            rateIdField.value = button.dataset.rateId;
            Object.entries(fieldDatasetKeys).forEach(([field, datasetKey]) => {
                document.getElementById(field).value = button.dataset[datasetKey] ?? '';
            });
            document.getElementById('is_active').checked = button.dataset.isActive === '1';
            formTitle.textContent = `Modifier ${button.dataset.regimeCode}`;
            submitButton.textContent = 'Enregistrer les modifications';
            dialog.showModal();
        });
    });

    @if($errors->any())
        dialog.showModal();
    @endif
</script>
@endpush
