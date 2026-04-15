@extends('layouts.app')

@section('title', 'Travailleurs | CNSS')
@section('page_title', 'Travailleurs')
@section('page_subtitle', 'Gestion des assures salaries')

@push('styles')
<style>
    .worker-page {
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

    .kpi {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .32rem .72rem;
        font-size: .82rem;
        font-weight: 700;
        color: #3641f5;
        background: #ecf3ff;
        border: 1px solid #dde9ff;
        margin-bottom: .8rem;
    }

    .grid {
        display: grid;
        gap: .72rem;
        grid-template-columns: repeat(2, minmax(0, 1fr));
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
        font-size: .83rem;
        font-weight: 600;
    }

    .control {
        height: 44px;
        width: 100%;
        border: 1px solid #d0d5dd;
        border-radius: 10px;
        padding: .55rem .78rem;
        font: inherit;
        font-size: .89rem;
        color: #101828;
        background: #fff;
        transition: border-color .15s ease, box-shadow .15s ease;
    }

    .control:focus {
        outline: 0;
        border-color: #9cb9ff;
        box-shadow: 0 0 0 4px rgba(70, 95, 255, 0.12);
    }

    .actions {
        display: flex;
        gap: .55rem;
        margin-top: .15rem;
        flex-wrap: wrap;
    }

    .btn {
        border: 0;
        border-radius: 10px;
        padding: .62rem .9rem;
        font: inherit;
        font-size: .86rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .btn-primary {
        color: #fff;
        background: #465fff;
    }

    .btn-outline {
        color: #344054;
        background: #fff;
        border: 1px solid #d0d5dd;
    }

    .btn-danger {
        color: #fff;
        background: #f04438;
    }

    .toolbar {
        display: flex;
        gap: .6rem;
        align-items: center;
        margin-bottom: .8rem;
        flex-wrap: wrap;
    }

    .toolbar .control {
        min-width: 220px;
        flex: 1;
    }

    .toolbar-actions {
        display: inline-flex;
        gap: .5rem;
    }

    .table-wrap {
        overflow: auto;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
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

    .row-actions {
        display: inline-flex;
        gap: .4rem;
    }

    .row-actions .btn {
        padding: .42rem .64rem;
        font-size: .78rem;
    }

    .status-text {
        min-height: 1.1rem;
        margin: .65rem 0 0;
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

    .empty {
        padding: 1.2rem;
        text-align: center;
        color: #667085;
        font-size: .9rem;
    }

    .panel-hidden {
        display: none;
    }

    @media (max-width: 920px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="worker-page">
    <article class="panel panel-hidden" id="worker-form-panel">
        <h2 id="form-title">Ajouter un travailleur</h2>
        <form id="worker-form">
            <input type="hidden" id="worker-id">
            <div class="grid">
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
                <div class="field">
                    <label for="status">Statut</label>
                    <select class="control" id="status" name="status">
                        <option value="ACTIVE">ACTIVE</option>
                        <option value="SUSPENDED">SUSPENDED</option>
                        <option value="INACTIVE">INACTIVE</option>
                    </select>
                </div>
                <div class="field">
                    <label for="employer_id">Employeur</label>
                    <select class="control" id="employer_id" name="employer_id" required></select>
                </div>
                <div class="field">
                    <label for="employment_start_date">Date embauche</label>
                    <input class="control" id="employment_start_date" name="employment_start_date" type="date" required>
                </div>
                <div class="field">
                    <label for="contract_type">Type contrat</label>
                    <input class="control" id="contract_type" name="contract_type" maxlength="30">
                </div>
                <div class="field full">
                    <label for="base_salary">Salaire de base</label>
                    <input class="control" id="base_salary" name="base_salary" type="number" min="0" step="0.01">
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary" id="save-btn">Enregistrer</button>
                <button type="button" class="btn btn-outline" id="reset-btn">Reinitialiser</button>
                <button type="button" class="btn btn-outline" id="close-form-btn">Fermer</button>
            </div>
        </form>
        <p id="status-message" class="status-text"></p>
    </article>

    <article class="panel">
        <div class="kpi" id="kpi-count">0 travailleur</div>
        <div class="toolbar">
            <input class="control" id="search-input" placeholder="Rechercher par nom, numero SS, CIN, employeur...">
            <div class="toolbar-actions">
                <button id="toggle-form-btn" class="btn btn-primary" type="button">Ajouter un travailleur</button>
                <button id="reload-btn" class="btn btn-outline" type="button">Rafraichir</button>
            </div>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Numero SS</th>
                    <th>Nom complet</th>
                    <th>CIN</th>
                    <th>Employeur</th>
                    <th>Date embauche</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="worker-table-body"></tbody>
            </table>
        </div>
    </article>
</div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    const state = {
        workers: [],
        filtered: [],
        employers: [],
    };

    const els = {
        form: document.getElementById('worker-form'),
        formPanel: document.getElementById('worker-form-panel'),
        formTitle: document.getElementById('form-title'),
        toggleFormBtn: document.getElementById('toggle-form-btn'),
        workerId: document.getElementById('worker-id'),
        employerSelect: document.getElementById('employer_id'),
        searchInput: document.getElementById('search-input'),
        tableBody: document.getElementById('worker-table-body'),
        reloadBtn: document.getElementById('reload-btn'),
        resetBtn: document.getElementById('reset-btn'),
        closeFormBtn: document.getElementById('close-form-btn'),
        saveBtn: document.getElementById('save-btn'),
        statusMessage: document.getElementById('status-message'),
        kpiCount: document.getElementById('kpi-count'),
    };

    const formFields = [
        'social_security_number',
        'national_id',
        'first_name',
        'last_name',
        'birth_date',
        'gender',
        'status',
        'employer_id',
        'employment_start_date',
        'contract_type',
        'base_salary',
    ];

    function setStatus(message, type = '') {
        els.statusMessage.textContent = message;
        els.statusMessage.className = type ? `status-text ${type}` : 'status-text';
    }

    function clearForm() {
        els.form.reset();
        els.workerId.value = '';
        document.getElementById('status').value = 'ACTIVE';
        document.getElementById('gender').value = '';
        document.getElementById('employment_start_date').value = new Date().toISOString().slice(0, 10);
        els.formTitle.textContent = 'Ajouter un travailleur';
        els.toggleFormBtn.textContent = 'Ajouter un travailleur';
        setStatus('');
    }

    function showForm() {
        els.formPanel.classList.remove('panel-hidden');
        els.toggleFormBtn.textContent = 'Masquer formulaire';
    }

    function hideForm() {
        els.formPanel.classList.add('panel-hidden');
        els.toggleFormBtn.textContent = 'Ajouter un travailleur';
    }

    function toggleAddForm() {
        const isHidden = els.formPanel.classList.contains('panel-hidden');
        if (isHidden) {
            clearForm();
            showForm();
            return;
        }

        clearForm();
        hideForm();
    }

    function fillForm(worker) {
        showForm();
        els.workerId.value = worker.id;
        formFields.forEach((field) => {
            const input = document.getElementById(field);
            if (input) {
                input.value = worker[field] ?? '';
            }
        });
        els.formTitle.textContent = `Modifier ${worker.first_name} ${worker.last_name}`;
        els.toggleFormBtn.textContent = 'Masquer formulaire';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function badgeClass(status) {
        if (status === 'SUSPENDED') return 'badge badge-suspended';
        if (status === 'INACTIVE') return 'badge badge-inactive';
        return 'badge badge-active';
    }

    function renderWorkers() {
        const count = state.filtered.length;
        els.kpiCount.textContent = `${count} travailleur${count > 1 ? 's' : ''}`;

        if (count === 0) {
            els.tableBody.innerHTML = '<tr><td colspan="7" class="empty">Aucun travailleur trouve.</td></tr>';
            return;
        }

        els.tableBody.innerHTML = state.filtered.map((item) => `
            <tr>
                <td>${escapeHtml(item.social_security_number || '-')}</td>
                <td>${escapeHtml(`${item.first_name || ''} ${item.last_name || ''}`.trim() || '-')}</td>
                <td>${escapeHtml(item.national_id || '-')}</td>
                <td>${escapeHtml(item.employer_name || '-')}</td>
                <td>${escapeHtml(item.employment_start_date || '-')}</td>
                <td><span class="${badgeClass(item.status)}">${escapeHtml(item.status || 'ACTIVE')}</span></td>
                <td>
                    <div class="row-actions">
                        <button class="btn btn-outline" type="button" data-action="edit" data-id="${item.id}">Modifier</button>
                        <button class="btn btn-danger" type="button" data-action="delete" data-id="${item.id}">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function filterWorkers() {
        const term = els.searchInput.value.trim().toLowerCase();
        if (!term) {
            state.filtered = [...state.workers];
            renderWorkers();
            return;
        }

        state.filtered = state.workers.filter((item) => {
            const blob = [
                item.social_security_number,
                item.national_id,
                item.first_name,
                item.last_name,
                item.employer_name,
                item.status,
            ].join(' ').toLowerCase();
            return blob.includes(term);
        });

        renderWorkers();
    }

    function collectFormPayload() {
        const payload = {};
        formFields.forEach((field) => {
            const value = document.getElementById(field).value.trim();
            payload[field] = value === '' ? null : value;
        });

        payload.social_security_number = payload.social_security_number || '';
        payload.first_name = payload.first_name || '';
        payload.last_name = payload.last_name || '';
        payload.status = payload.status || 'ACTIVE';
        payload.employer_id = payload.employer_id ? Number(payload.employer_id) : null;
        return payload;
    }

    async function loadEmployers() {
        const response = await fetch('/api/employers', {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Impossible de charger la liste des employeurs.');
        }

        state.employers = await response.json();
        els.employerSelect.innerHTML = state.employers
            .map((item) => `<option value="${item.id}">${escapeHtml(item.legal_name)} (${escapeHtml(item.affiliation_number)})</option>`)
            .join('');
    }

    async function loadWorkers() {
        setStatus('Chargement des travailleurs...');
        els.reloadBtn.disabled = true;

        try {
            const response = await fetch('/api/workers');
            if (!response.ok) {
                throw new Error('Impossible de charger les travailleurs.');
            }

            state.workers = await response.json();
            state.filtered = [...state.workers];
            renderWorkers();
            setStatus(`${state.workers.length} travailleur(s) charge(s).`, 'ok');
        } catch (error) {
            setStatus(error.message || 'Erreur de chargement.', 'error');
        } finally {
            els.reloadBtn.disabled = false;
        }
    }

    async function submitWorker(event) {
        event.preventDefault();
        setStatus('Enregistrement en cours...');
        els.saveBtn.disabled = true;

        const workerId = els.workerId.value;
        const payload = collectFormPayload();
        const isEdit = Boolean(workerId);
        const url = isEdit ? `/api/workers/${workerId}` : '/api/workers';
        const method = isEdit ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method,
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
                throw new Error('Echec de l enregistrement.');
            }

            clearForm();
            hideForm();
            await loadWorkers();
            setStatus(isEdit ? 'Travailleur mis a jour.' : 'Travailleur cree.', 'ok');
        } catch (error) {
            setStatus(error.message || 'Erreur de sauvegarde.', 'error');
        } finally {
            els.saveBtn.disabled = false;
        }
    }

    async function deleteWorker(id) {
        const confirmed = confirm('Supprimer ce travailleur ?');
        if (!confirmed) {
            return;
        }

        setStatus('Suppression en cours...');

        try {
            const response = await fetch(`/api/workers/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error('Suppression impossible.');
            }

            if (String(els.workerId.value) === String(id)) {
                clearForm();
                hideForm();
            }

            await loadWorkers();
            setStatus('Travailleur supprime.', 'ok');
        } catch (error) {
            setStatus(error.message || 'Erreur de suppression.', 'error');
        }
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    els.form.addEventListener('submit', submitWorker);
    els.resetBtn.addEventListener('click', clearForm);
    els.closeFormBtn.addEventListener('click', () => {
        clearForm();
        hideForm();
    });
    els.toggleFormBtn.addEventListener('click', toggleAddForm);
    els.searchInput.addEventListener('input', filterWorkers);
    els.reloadBtn.addEventListener('click', loadWorkers);

    els.tableBody.addEventListener('click', (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const button = target.closest('button[data-action]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        const worker = state.workers.find((item) => item.id === id);
        if (!worker) {
            return;
        }

        if (button.dataset.action === 'edit') {
            fillForm(worker);
        }

        if (button.dataset.action === 'delete') {
            deleteWorker(id);
        }
    });

    async function init() {
        try {
            await loadEmployers();
            clearForm();
            await loadWorkers();
        } catch (error) {
            setStatus(error.message || 'Erreur d initialisation.', 'error');
        }
    }

    init();
</script>
@endpush
