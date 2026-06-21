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
    .form-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: .75rem; }
    .field { display: grid; gap: .35rem; }
    .field label { color: #344054; font-size: .82rem; font-weight: 700; }
    .control { width: 100%; min-height: 42px; border: 1px solid #d0d5dd; border-radius: 8px; padding: .55rem .7rem; font: inherit; }
    .control:focus { outline: 0; border-color: #008f83; box-shadow: 0 0 0 4px rgba(0, 143, 131, .13); }
    .switch-field { display: flex; align-items: center; gap: .5rem; min-height: 42px; padding-top: 1.25rem; }
    .actions { display: flex; gap: .55rem; margin-top: .85rem; }
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
    @media (max-width: 1050px) { .form-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 650px) { .form-grid { grid-template-columns: 1fr; } .switch-field { padding-top: 0; } }
</style>
@endpush

@section('content')
<div class="settings-page">
    @if(session('status'))
        <div class="notice">{{ session('status') }}</div>
    @endif

    @if($errors->any())
        <div class="errors">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="panel" id="rate-form-panel">
        <div class="panel-head">
            <h2 id="form-title">Nouvelle modalite</h2>
            <button class="btn btn-outline" id="reset-form" type="button">Reinitialiser</button>
        </div>

        <form id="rate-form" method="POST" action="{{ route('contribution-rates.store') }}" data-store-action="{{ route('contribution-rates.store') }}">
            @csrf
            <input id="method-field" type="hidden" name="_method" value="POST">
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
                    <label for="floor_amount">Plancher cotisable</label>
                    <input class="control" id="floor_amount" name="floor_amount" type="number" min="0" step="0.01" value="{{ old('floor_amount') }}">
                </div>
                <div class="field">
                    <label for="ceiling_amount">Plafond cotisable</label>
                    <input class="control" id="ceiling_amount" name="ceiling_amount" type="number" min="0" step="0.01" value="{{ old('ceiling_amount') }}">
                </div>
                <label class="switch-field">
                    <input id="is_active" name="is_active" type="checkbox" value="1" @checked(old('is_active', true))>
                    Modalite active
                </label>
            </div>
            <div class="actions">
                <button class="btn btn-primary" id="submit-button" type="submit">Creer la modalite</button>
            </div>
        </form>
    </section>

    <section class="panel">
        <div class="panel-head">
            <h2>Historique des modalites</h2>
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
                        <td>{{ $rate->floor_amount ?? '-' }}</td>
                        <td>{{ $rate->ceiling_amount ?? '-' }}</td>
                        <td><span class="badge {{ $rate->is_active ? 'badge-active' : 'badge-inactive' }}">{{ $rate->is_active ? 'ACTIVE' : 'INACTIVE' }}</span></td>
                        <td>
                            <button
                                class="btn btn-outline edit-rate"
                                type="button"
                                data-action="{{ route('contribution-rates.update', $rate) }}"
                                data-regime-code="{{ $rate->regime_code }}"
                                data-effective-from="{{ $rate->effective_from?->format('Y-m-d') }}"
                                data-effective-to="{{ $rate->effective_to?->format('Y-m-d') }}"
                                data-employer-rate="{{ $rate->employer_rate }}"
                                data-worker-rate="{{ $rate->worker_rate }}"
                                data-floor-amount="{{ $rate->floor_amount }}"
                                data-ceiling-amount="{{ $rate->ceiling_amount }}"
                                data-is-active="{{ $rate->is_active ? '1' : '0' }}"
                            >Modifier</button>
                        </td>
                    </tr>
                @empty
                    <tr><td class="empty" colspan="9">Aucune modalite configuree.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    const form = document.getElementById('rate-form');
    const methodField = document.getElementById('method-field');
    const formTitle = document.getElementById('form-title');
    const submitButton = document.getElementById('submit-button');
    const fieldDatasetKeys = {
        regime_code: 'regimeCode',
        effective_from: 'effectiveFrom',
        effective_to: 'effectiveTo',
        employer_rate: 'employerRate',
        worker_rate: 'workerRate',
        floor_amount: 'floorAmount',
        ceiling_amount: 'ceilingAmount',
    };

    function resetForm() {
        form.reset();
        form.action = form.dataset.storeAction;
        methodField.value = 'POST';
        document.getElementById('regime_code').value = 'GENERAL';
        document.getElementById('is_active').checked = true;
        formTitle.textContent = 'Nouvelle modalite';
        submitButton.textContent = 'Creer la modalite';
    }

    document.getElementById('reset-form').addEventListener('click', resetForm);

    document.querySelectorAll('.edit-rate').forEach((button) => {
        button.addEventListener('click', () => {
            form.action = button.dataset.action;
            methodField.value = 'PUT';
            Object.entries(fieldDatasetKeys).forEach(([field, datasetKey]) => {
                document.getElementById(field).value = button.dataset[datasetKey] ?? '';
            });
            document.getElementById('is_active').checked = button.dataset.isActive === '1';
            formTitle.textContent = `Modifier ${button.dataset.regimeCode}`;
            submitButton.textContent = 'Enregistrer les modifications';
            document.getElementById('rate-form-panel').scrollIntoView({ behavior: 'smooth' });
        });
    });
</script>
@endpush
