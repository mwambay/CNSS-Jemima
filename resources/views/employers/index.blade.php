@extends('layouts.app')

@section('title', 'Employeurs | CNSS')
@section('page_title', 'Employeurs')
@section('page_subtitle', 'Gestion des affiliations employeurs')

@push('styles')
<style>
    .employer-page {
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

    textarea.control {
        min-height: 88px;
        height: auto;
        resize: vertical;
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

    .toolbar-actions {
        display: inline-flex;
        gap: .5rem;
    }

    .toolbar .control {
        min-width: 220px;
        flex: 1;
    }

    .table-wrap {
        overflow: auto;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 840px;
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

    .badge-closed {
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

    .panel-hidden {
        display: none;
    }

    .empty {
        padding: 1.2rem;
        text-align: center;
        color: #667085;
        font-size: .9rem;
    }

    @media (max-width: 1080px) {
        .employer-page {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .grid {
            grid-template-columns: 1fr;
        }

        .actions {
            flex-wrap: wrap;
        }
    }
</style>
@endpush

@section('content')
    <div class="employer-page">
        <article class="panel panel-hidden" id="employer-form-panel">
            <h2 id="form-title">Ajouter un employeur</h2>
            <form id="employer-form">
                <input type="hidden" id="employer-id">
                <div class="grid">
                    <div class="field">
                        <label for="affiliation_number">Numero affiliation</label>
                        <input class="control" id="affiliation_number" name="affiliation_number" required maxlength="30">
                    </div>
                    <div class="field">
                        <label for="legal_name">Raison sociale</label>
                        <input class="control" id="legal_name" name="legal_name" required maxlength="200">
                    </div>
                    <div class="field">
                        <label for="tax_id">NIF</label>
                        <input class="control" id="tax_id" name="tax_id" maxlength="50">
                    </div>
                    <div class="field">
                        <label for="registration_number">Registre commerce</label>
                        <input class="control" id="registration_number" name="registration_number" maxlength="50">
                    </div>
                    <div class="field">
                        <label for="legal_form">Forme juridique</label>
                        <input class="control" id="legal_form" name="legal_form" maxlength="50">
                    </div>
                    <div class="field">
                        <label for="sector">Secteur</label>
                        <input class="control" id="sector" name="sector" maxlength="100">
                    </div>
                    <div class="field">
                        <label for="status">Statut</label>
                        <select class="control" id="status" name="status">
                            <option value="ACTIVE">ACTIVE</option>
                            <option value="SUSPENDED">SUSPENDED</option>
                            <option value="CLOSED">CLOSED</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="verification_status">Verification</label>
                        <select class="control" id="verification_status" name="verification_status">
                            <option value="PENDING">PENDING</option>
                            <option value="VERIFIED">VERIFIED</option>
                            <option value="REJECTED">REJECTED</option>
                        </select>
                    </div>
                    <div class="field">
                        <label for="phone">Telephone</label>
                        <input class="control" id="phone" name="phone" maxlength="30">
                    </div>
                    <div class="field">
                        <label for="email">Email</label>
                        <input class="control" id="email" name="email" type="email" maxlength="150">
                    </div>
                    <div class="field full">
                        <label for="address">Adresse</label>
                        <textarea class="control" id="address" name="address"></textarea>
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
            <div class="kpi" id="kpi-count">0 employeur</div>
            <div class="toolbar">
                <input class="control" id="search-input" placeholder="Rechercher par raison sociale, affiliation, NIF...">
                <div class="toolbar-actions">
                    <button id="toggle-form-btn" class="btn btn-primary" type="button">Ajouter un employeur</button>
                    <button id="reload-btn" class="btn btn-outline" type="button">Rafraichir</button>
                </div>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Affiliation</th>
                        <th>Raison sociale</th>
                        <th>NIF</th>
                        <th>Statut</th>
                        <th>Verification</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="employer-table-body"></tbody>
                </table>
            </div>
        </article>
    </div>
@endsection

@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';

    const state = {
        employers: [],
        filtered: [],
    };

    const els = {
        form: document.getElementById('employer-form'),
        formPanel: document.getElementById('employer-form-panel'),
        formTitle: document.getElementById('form-title'),
        toggleFormBtn: document.getElementById('toggle-form-btn'),
        employerId: document.getElementById('employer-id'),
        searchInput: document.getElementById('search-input'),
        tableBody: document.getElementById('employer-table-body'),
        reloadBtn: document.getElementById('reload-btn'),
        resetBtn: document.getElementById('reset-btn'),
        closeFormBtn: document.getElementById('close-form-btn'),
        saveBtn: document.getElementById('save-btn'),
        statusMessage: document.getElementById('status-message'),
        kpiCount: document.getElementById('kpi-count'),
    };

    const formFields = [
        'affiliation_number',
        'legal_name',
        'tax_id',
        'registration_number',
        'legal_form',
        'sector',
        'status',
        'verification_status',
        'phone',
        'email',
        'address',
    ];

    function setStatus(message, type = '') {
        els.statusMessage.textContent = message;
        els.statusMessage.className = type ? `status-text ${type}` : 'status-text';
    }

    function clearForm() {
        els.form.reset();
        els.employerId.value = '';
        document.getElementById('status').value = 'ACTIVE';
        document.getElementById('verification_status').value = 'PENDING';
        els.formTitle.textContent = 'Ajouter un employeur';
        els.toggleFormBtn.textContent = 'Ajouter un employeur';
        setStatus('');
    }

    function showForm() {
        els.formPanel.classList.remove('panel-hidden');
        els.toggleFormBtn.textContent = 'Masquer formulaire';
    }

    function hideForm() {
        els.formPanel.classList.add('panel-hidden');
        els.toggleFormBtn.textContent = 'Ajouter un employeur';
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

    function fillForm(employer) {
        showForm();
        els.employerId.value = employer.id;
        formFields.forEach((field) => {
            const input = document.getElementById(field);
            input.value = employer[field] ?? '';
        });
        els.formTitle.textContent = `Modifier ${employer.legal_name}`;
        els.toggleFormBtn.textContent = 'Masquer formulaire';
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function badgeClass(status) {
        if (status === 'SUSPENDED') return 'badge badge-suspended';
        if (status === 'CLOSED') return 'badge badge-closed';
        return 'badge badge-active';
    }

    function renderEmployers() {
        const count = state.filtered.length;
        els.kpiCount.textContent = `${count} employeur${count > 1 ? 's' : ''}`;

        if (count === 0) {
            els.tableBody.innerHTML = '<tr><td colspan="6" class="empty">Aucun employeur trouve.</td></tr>';
            return;
        }

        els.tableBody.innerHTML = state.filtered.map((item) => `
            <tr>
                <td>${escapeHtml(item.affiliation_number || '-')}</td>
                <td>${escapeHtml(item.legal_name || '-')}</td>
                <td>${escapeHtml(item.tax_id || '-')}</td>
                <td><span class="${badgeClass(item.status)}">${escapeHtml(item.status || 'ACTIVE')}</span></td>
                <td>${escapeHtml(item.verification_status || '-')}</td>
                <td>
                    <div class="row-actions">
                        <a class="btn btn-outline" href="/employeurs/${item.id}">Detail</a>
                        <button class="btn btn-outline" type="button" data-action="edit" data-id="${item.id}">Modifier</button>
                        <button class="btn btn-danger" type="button" data-action="delete" data-id="${item.id}">Supprimer</button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    function filterEmployers() {
        const term = els.searchInput.value.trim().toLowerCase();
        if (!term) {
            state.filtered = [...state.employers];
            renderEmployers();
            return;
        }

        state.filtered = state.employers.filter((item) => {
            const blob = [
                item.legal_name,
                item.affiliation_number,
                item.tax_id,
                item.phone,
                item.email,
                item.status,
                item.verification_status,
            ].join(' ').toLowerCase();
            return blob.includes(term);
        });

        renderEmployers();
    }

    function collectFormPayload() {
        const payload = {};
        formFields.forEach((field) => {
            const value = document.getElementById(field).value.trim();
            payload[field] = value === '' ? null : value;
        });

        payload.affiliation_number = payload.affiliation_number || '';
        payload.legal_name = payload.legal_name || '';
        payload.status = payload.status || 'ACTIVE';
        payload.verification_status = payload.verification_status || 'PENDING';
        return payload;
    }

    async function loadEmployers() {
        setStatus('Chargement des employeurs...');
        els.reloadBtn.disabled = true;

        try {
            const response = await fetch('/api/employers');
            if (!response.ok) {
                throw new Error('Impossible de charger les employeurs.');
            }

            state.employers = await response.json();
            state.filtered = [...state.employers];
            renderEmployers();
            setStatus(`${state.employers.length} employeur(s) charge(s).`, 'ok');
        } catch (error) {
            setStatus(error.message || 'Erreur de chargement.', 'error');
        } finally {
            els.reloadBtn.disabled = false;
        }
    }

    async function submitEmployer(event) {
        event.preventDefault();
        setStatus('Enregistrement en cours...');
        els.saveBtn.disabled = true;

        const employerId = els.employerId.value;
        const payload = collectFormPayload();
        const isEdit = Boolean(employerId);
        const url = isEdit ? `/api/employers/${employerId}` : '/api/employers';
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
            await loadEmployers();
            setStatus(isEdit ? 'Employeur mis a jour.' : 'Employeur cree.', 'ok');
        } catch (error) {
            setStatus(error.message || 'Erreur de sauvegarde.', 'error');
        } finally {
            els.saveBtn.disabled = false;
        }
    }

    async function deleteEmployer(id) {
        const confirmed = confirm('Supprimer cet employeur ?');
        if (!confirmed) {
            return;
        }

        setStatus('Suppression en cours...');

        try {
            const response = await fetch(`/api/employers/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error('Suppression impossible.');
            }

            if (String(els.employerId.value) === String(id)) {
                clearForm();
                hideForm();
            }

            await loadEmployers();
            setStatus('Employeur supprime.', 'ok');
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

    els.form.addEventListener('submit', submitEmployer);
    els.resetBtn.addEventListener('click', clearForm);
    els.closeFormBtn.addEventListener('click', () => {
        clearForm();
        hideForm();
    });
    els.toggleFormBtn.addEventListener('click', toggleAddForm);
    els.searchInput.addEventListener('input', filterEmployers);
    els.reloadBtn.addEventListener('click', loadEmployers);

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
        const employer = state.employers.find((item) => item.id === id);
        if (!employer) {
            return;
        }

        if (button.dataset.action === 'edit') {
            fillForm(employer);
        }

        if (button.dataset.action === 'delete') {
            deleteEmployer(id);
        }
    });

    loadEmployers();
</script>
@endpush
