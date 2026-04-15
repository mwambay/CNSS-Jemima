@extends('layouts.app')

@section('title', 'Detail employeur | CNSS')
@section('page_title', 'Detail employeur')
@section('page_subtitle', 'Informations employeur et travailleurs rattaches')

@push('styles')
<style>
    .details-page {
        display: grid;
        gap: 1rem;
        grid-template-columns: 1fr;
    }

    .panel {
        background: #fff;
        border: 1px solid #e4e7ec;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1), 0 1px 2px rgba(16, 24, 40, 0.06);
        padding: 1rem;
    }

    .panel h2 {
        margin: 0 0 .9rem;
        color: #101828;
        font-size: 1.03rem;
        font-weight: 700;
    }

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: .6rem;
        margin-bottom: .8rem;
        flex-wrap: wrap;
    }

    .tabs {
        display: flex;
        gap: .45rem;
        border-bottom: 1px solid #e4e7ec;
        margin-bottom: 1rem;
        padding-bottom: .45rem;
    }

    .tab-btn {
        border: 1px solid transparent;
        background: transparent;
        color: #667085;
        border-radius: 10px;
        padding: .5rem .8rem;
        font: inherit;
        font-size: .84rem;
        font-weight: 700;
        cursor: pointer;
    }

    .tab-btn:hover {
        background: #f9fafb;
        color: #344054;
    }

    .tab-btn.active {
        background: #ecf3ff;
        color: #3641f5;
        border-color: #dde9ff;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .75rem;
    }

    .meta-item {
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        padding: .7rem .8rem;
        background: #fcfcfd;
    }

    .meta-label {
        margin: 0 0 .35rem;
        color: #667085;
        font-size: .77rem;
        letter-spacing: .03em;
        text-transform: uppercase;
        font-weight: 700;
    }

    .meta-value {
        margin: 0;
        color: #101828;
        font-size: .9rem;
        font-weight: 600;
        word-break: break-word;
    }

    .kpi {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .3rem .68rem;
        font-size: .82rem;
        font-weight: 700;
        color: #3641f5;
        background: #ecf3ff;
        border: 1px solid #dde9ff;
    }

    .toolbar-right {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .btn {
        border: 0;
        border-radius: 10px;
        padding: .56rem .86rem;
        font: inherit;
        font-size: .84rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-outline {
        background: #fff;
        color: #344054;
        border: 1px solid #d0d5dd;
    }

    .btn-primary {
        background: #465fff;
        color: #fff;
        border: 1px solid #465fff;
    }

    .create-worker-card {
        margin-top: 1rem;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        padding: .9rem;
        background: #fcfcfd;
    }

    .is-hidden {
        display: none;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: .7rem;
    }

    .field {
        display: grid;
        gap: .35rem;
    }

    .field.full {
        grid-column: 1 / -1;
    }

    .field label {
        color: #344054;
        font-size: .82rem;
        font-weight: 600;
    }

    .control {
        height: 42px;
        width: 100%;
        border: 1px solid #d0d5dd;
        border-radius: 10px;
        padding: .55rem .72rem;
        font: inherit;
        font-size: .88rem;
        color: #101828;
        background: #fff;
    }

    .control:focus {
        outline: 0;
        border-color: #9cb9ff;
        box-shadow: 0 0 0 4px rgba(70, 95, 255, 0.12);
    }

    .form-actions {
        display: flex;
        gap: .5rem;
        margin-top: .8rem;
        flex-wrap: wrap;
    }

    .status-text {
        min-height: 1.1rem;
        margin: .55rem 0 0;
        font-size: .83rem;
        color: #667085;
        font-weight: 500;
    }

    .status-text.ok {
        color: #12b76a;
        font-weight: 600;
    }

    .status-text.error {
        color: #b42318;
        font-weight: 600;
    }

    .table-wrap {
        overflow: auto;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 900px;
    }

    th,
    td {
        text-align: left;
        padding: .72rem .75rem;
        border-bottom: 1px solid #e4e7ec;
        font-size: .88rem;
        color: #344054;
        vertical-align: middle;
    }

    th {
        font-size: .77rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        color: #667085;
        background: #f9fafb;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .2rem .58rem;
        font-size: .74rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .badge-active {
        color: #027a48;
        background: #ecfdf3;
    }

    .badge-suspended {
        color: #b54708;
        background: #fffaeb;
    }

    .badge-inactive {
        color: #b42318;
        background: #fef3f2;
    }

    .empty {
        padding: 1.2rem;
        text-align: center;
        color: #667085;
        font-size: .9rem;
    }

    @media (max-width: 1024px) {
        .meta-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 700px) {
        .meta-grid {
            grid-template-columns: 1fr;
        }

        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="details-page">
    <article class="panel">
        <div class="toolbar">
            <h2>Fiche employeur</h2>
            <a class="btn btn-outline" href="{{ route('employers.interface') }}">Retour a la liste</a>
        </div>

        <div class="tabs" role="tablist" aria-label="Details employeur">
            <button class="tab-btn active" type="button" role="tab" aria-selected="true" data-tab-target="employer-infos-tab">Infos</button>
            <button class="tab-btn" type="button" role="tab" aria-selected="false" data-tab-target="employer-workers-tab">Travailleurs</button>
        </div>

        <section id="employer-infos-tab" class="tab-pane active" role="tabpanel">
            <div class="meta-grid">
                <div class="meta-item">
                    <p class="meta-label">Numero affiliation</p>
                    <p class="meta-value">{{ $employer->affiliation_number ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Raison sociale</p>
                    <p class="meta-value">{{ $employer->legal_name ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">NIF</p>
                    <p class="meta-value">{{ $employer->tax_id ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Registre commerce</p>
                    <p class="meta-value">{{ $employer->registration_number ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Forme juridique</p>
                    <p class="meta-value">{{ $employer->legal_form ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Secteur</p>
                    <p class="meta-value">{{ $employer->sector ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Statut</p>
                    <p class="meta-value">{{ $employer->status ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Verification</p>
                    <p class="meta-value">{{ $employer->verification_status ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Telephone</p>
                    <p class="meta-value">{{ $employer->phone ?? '-' }}</p>
                </div>
                <div class="meta-item">
                    <p class="meta-label">Email</p>
                    <p class="meta-value">{{ $employer->email ?? '-' }}</p>
                </div>
                <div class="meta-item" style="grid-column: 1 / -1;">
                    <p class="meta-label">Adresse</p>
                    <p class="meta-value">{{ $employer->address ?? '-' }}</p>
                </div>
            </div>
        </section>

        <section id="employer-workers-tab" class="tab-pane" role="tabpanel">
            <div class="toolbar">
                <h2>Travailleurs rattaches</h2>
                <div class="toolbar-right">
                    <span class="kpi">{{ $workers->count() }} travailleur{{ $workers->count() > 1 ? 's' : '' }}</span>
                    @if($canManageWorkers)
                        <button id="toggle-worker-form-btn" class="btn btn-primary" type="button">Ajouter un travailleur</button>
                    @endif
                </div>
            </div>

            <div id="workers-list-block" class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Numero SS</th>
                        <th>Nom complet</th>
                        <th>CIN</th>
                        <th>Statut</th>
                        <th>Contrat</th>
                        <th>Date embauche</th>
                        <th>Salaire base</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($workers as $worker)
                        <tr>
                            <td>{{ $worker['social_security_number'] ?? '-' }}</td>
                            <td>{{ $worker['full_name'] ?? '-' }}</td>
                            <td>{{ $worker['national_id'] ?? '-' }}</td>
                            <td>
                                @php $status = $worker['status'] ?? 'ACTIVE'; @endphp
                                <span class="badge {{ $status === 'SUSPENDED' ? 'badge-suspended' : ($status === 'INACTIVE' ? 'badge-inactive' : 'badge-active') }}">{{ $status }}</span>
                            </td>
                            <td>{{ $worker['contract_type'] ?? '-' }}</td>
                            <td>{{ $worker['start_date'] ?? '-' }}</td>
                            <td>{{ $worker['base_salary'] ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">Aucun travailleur rattache a cet employeur.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($canManageWorkers)
                <div id="create-worker-card" class="create-worker-card is-hidden">
                    <h2>Ajouter un travailleur a cet employeur</h2>
                    <form id="create-worker-form">
                        <input type="hidden" name="employer_id" value="{{ $employer->id }}">
                        <div class="form-grid">
                            <div class="field">
                                <label for="social_security_number">Numero SS</label>
                                <input class="control" id="social_security_number" name="social_security_number" required maxlength="50">
                            </div>
                            <div class="field">
                                <label for="national_id">Numero CIN</label>
                                <input class="control" id="national_id" name="national_id" maxlength="50">
                            </div>
                            <div class="field">
                                <label for="first_name">Prenom</label>
                                <input class="control" id="first_name" name="first_name" required maxlength="100">
                            </div>
                            <div class="field">
                                <label for="last_name">Nom</label>
                                <input class="control" id="last_name" name="last_name" required maxlength="100">
                            </div>
                            <div class="field">
                                <label for="employment_start_date">Date embauche</label>
                                <input class="control" id="employment_start_date" name="employment_start_date" type="date" required>
                            </div>
                            <div class="field">
                                <label for="status">Statut</label>
                                <select class="control" id="status" name="status">
                                    <option value="ACTIVE">ACTIVE</option>
                                    <option value="SUSPENDED">SUSPENDED</option>
                                    <option value="INACTIVE">INACTIVE</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="contract_type">Type contrat</label>
                                <input class="control" id="contract_type" name="contract_type" maxlength="30">
                            </div>
                            <div class="field">
                                <label for="base_salary">Salaire de base</label>
                                <input class="control" id="base_salary" name="base_salary" type="number" min="0" step="0.01">
                            </div>
                            <div class="field">
                                <label for="birth_date">Date de naissance</label>
                                <input class="control" id="birth_date" name="birth_date" type="date">
                            </div>
                            <div class="field">
                                <label for="gender">Genre</label>
                                <select class="control" id="gender" name="gender">
                                    <option value="">--</option>
                                    <option value="M">M</option>
                                    <option value="F">F</option>
                                    <option value="OTHER">OTHER</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button id="create-worker-btn" class="btn btn-primary" type="submit">Ajouter</button>
                            <button id="reset-worker-btn" class="btn btn-outline" type="button">Reinitialiser</button>
                            <button id="cancel-worker-create-btn" class="btn btn-outline" type="button">Annuler</button>
                        </div>
                    </form>
                    <p id="create-worker-status" class="status-text"></p>
                </div>
            @endif
        </section>
    </article>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const tabButtons = document.querySelectorAll('[data-tab-target]');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const targetId = button.dataset.tabTarget;

            tabButtons.forEach((tabButton) => {
                tabButton.classList.remove('active');
                tabButton.setAttribute('aria-selected', 'false');
            });

            tabPanes.forEach((pane) => {
                pane.classList.remove('active');
            });

            button.classList.add('active');
            button.setAttribute('aria-selected', 'true');

            const targetPane = document.getElementById(targetId);
            if (targetPane) {
                targetPane.classList.add('active');
            }
        });
    });

    const createWorkerForm = document.getElementById('create-worker-form');
    const workersListBlock = document.getElementById('workers-list-block');
    const createWorkerCard = document.getElementById('create-worker-card');
    const toggleWorkerFormBtn = document.getElementById('toggle-worker-form-btn');
    const cancelWorkerCreateBtn = document.getElementById('cancel-worker-create-btn');
    const createWorkerBtn = document.getElementById('create-worker-btn');
    const resetWorkerBtn = document.getElementById('reset-worker-btn');
    const createWorkerStatus = document.getElementById('create-worker-status');

    function showCreateWorkerForm() {
        if (workersListBlock) {
            workersListBlock.classList.add('is-hidden');
        }

        if (createWorkerCard) {
            createWorkerCard.classList.remove('is-hidden');
        }

        if (toggleWorkerFormBtn) {
            toggleWorkerFormBtn.textContent = 'Voir la liste';
        }
    }

    function showWorkersList() {
        if (workersListBlock) {
            workersListBlock.classList.remove('is-hidden');
        }

        if (createWorkerCard) {
            createWorkerCard.classList.add('is-hidden');
        }

        if (toggleWorkerFormBtn) {
            toggleWorkerFormBtn.textContent = 'Ajouter un travailleur';
        }
    }

    function setCreateWorkerStatus(message, type = '') {
        if (!createWorkerStatus) {
            return;
        }

        createWorkerStatus.textContent = message;
        createWorkerStatus.className = type ? `status-text ${type}` : 'status-text';
    }

    function normalizePayload(formData) {
        const payload = {
            employer_id: Number(formData.get('employer_id')),
            social_security_number: String(formData.get('social_security_number') || '').trim(),
            national_id: String(formData.get('national_id') || '').trim() || null,
            first_name: String(formData.get('first_name') || '').trim(),
            last_name: String(formData.get('last_name') || '').trim(),
            employment_start_date: String(formData.get('employment_start_date') || '').trim(),
            status: String(formData.get('status') || 'ACTIVE').trim() || 'ACTIVE',
            contract_type: String(formData.get('contract_type') || '').trim() || null,
            base_salary: String(formData.get('base_salary') || '').trim() || null,
            birth_date: String(formData.get('birth_date') || '').trim() || null,
            gender: String(formData.get('gender') || '').trim() || null,
        };

        return payload;
    }

    if (createWorkerForm) {
        toggleWorkerFormBtn?.addEventListener('click', () => {
            const formIsHidden = createWorkerCard?.classList.contains('is-hidden');

            if (formIsHidden) {
                showCreateWorkerForm();
                return;
            }

            showWorkersList();
        });

        resetWorkerBtn?.addEventListener('click', () => {
            createWorkerForm.reset();
            const startInput = document.getElementById('employment_start_date');
            if (startInput instanceof HTMLInputElement) {
                startInput.value = new Date().toISOString().slice(0, 10);
            }
            setCreateWorkerStatus('');
        });

        cancelWorkerCreateBtn?.addEventListener('click', () => {
            showWorkersList();
            setCreateWorkerStatus('');
        });

        const startInput = document.getElementById('employment_start_date');
        if (startInput instanceof HTMLInputElement && !startInput.value) {
            startInput.value = new Date().toISOString().slice(0, 10);
        }

        createWorkerForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            setCreateWorkerStatus('Creation du travailleur en cours...');

            if (createWorkerBtn) {
                createWorkerBtn.disabled = true;
            }

            try {
                const formData = new FormData(createWorkerForm);
                const payload = normalizePayload(formData);

                const response = await fetch('/api/workers', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: JSON.stringify(payload),
                });

                if (response.status === 422) {
                    const data = await response.json();
                    const errors = Object.values(data.errors || {}).flat().join(' ');
                    throw new Error(errors || 'Validation invalide.');
                }

                if (!response.ok) {
                    throw new Error('Creation impossible.');
                }

                setCreateWorkerStatus('Travailleur ajoute avec succes.', 'ok');
                window.location.reload();
            } catch (error) {
                const message = error instanceof Error ? error.message : 'Erreur lors de la creation.';
                setCreateWorkerStatus(message, 'error');
            } finally {
                if (createWorkerBtn) {
                    createWorkerBtn.disabled = false;
                }
            }
        });
    }
</script>
@endpush
