@extends('layouts.app')

@section('title', 'Declarations | CNSS')
@section('page_title', 'Declarations')
@section('page_subtitle', 'Gestion des declarations mensuelles')

@push('styles')
<style>
    .declaration-page {
        display: grid;
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .panel {
        background: #fff;
        border: 1px solid #e4e7ec;
        border-radius: 16px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.1), 0 1px 2px rgba(16, 24, 40, 0.06);
        padding: 1rem;
    }

    .panel h2 {
        margin: 0;
        color: #101828;
        font-size: 1.02rem;
        font-weight: 700;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        margin-bottom: .9rem;
        flex-wrap: wrap;
    }

    .toolbar-right {
        display: inline-flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .kpi {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .32rem .7rem;
        font-size: .82rem;
        font-weight: 700;
        color: #3641f5;
        background: #ecf3ff;
        border: 1px solid #dde9ff;
    }

    .grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .7rem;
    }

    .field {
        display: grid;
        gap: .35rem;
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
        justify-content: center;
    }

    .btn:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    .btn-primary {
        background: #465fff;
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        color: #344054;
        border: 1px solid #d0d5dd;
    }

    .btn-danger {
        background: #f04438;
        color: #fff;
    }

    .actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .table-wrap {
        overflow: auto;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 950px;
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
        padding: .2rem .55rem;
        font-size: .74rem;
        font-weight: 700;
        letter-spacing: .02em;
    }

    .badge-draft {
        color: #3641f5;
        background: #ecf3ff;
    }

    .badge-submitted {
        color: #b54708;
        background: #fffaeb;
    }

    .badge-validated {
        color: #027a48;
        background: #ecfdf3;
    }

    .badge-rejected {
        color: #b42318;
        background: #fef3f2;
    }

    .is-hidden {
        display: none;
    }

    .status-text {
        min-height: 1.1rem;
        margin: .6rem 0 0;
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

    .notice {
        border: 1px solid #fde272;
        background: #fffbeb;
        color: #854d0e;
        border-radius: 12px;
        padding: .8rem;
        font-size: .9rem;
        font-weight: 600;
    }

    @media (max-width: 960px) {
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="declaration-page">
    @if(!$canManageDeclarations)
        <article class="panel">
            <div class="notice">Acces restreint: vous n'avez pas les droits ADMIN pour gerer les declarations.</div>
        </article>
    @else
        <article class="panel">
            <div class="toolbar">
                <h2>Liste des declarations</h2>
                <div class="toolbar-right">
                    <span class="kpi" id="declaration-count">0 declaration</span>
                    <button id="toggle-declaration-form-btn" class="btn btn-primary" type="button">Nouvelle declaration</button>
                </div>
            </div>

            <form id="declaration-form" class="is-hidden">
                <div class="grid">
                    <div class="field">
                        <label for="employer_id">Employeur</label>
                        <select class="control" id="employer_id" name="employer_id" required></select>
                    </div>
                    <div class="field">
                        <label for="period_year">Annee</label>
                        <input class="control" id="period_year" name="period_year" type="number" min="2000" max="2100" required>
                    </div>
                    <div class="field">
                        <label for="period_month">Mois</label>
                        <input class="control" id="period_month" name="period_month" type="number" min="1" max="12" required>
                    </div>
                    <div class="field">
                        <label for="due_date">Date echeance</label>
                        <input class="control" id="due_date" name="due_date" type="date">
                    </div>
                </div>
                <div class="actions" style="margin-top:.8rem;">
                    <button id="save-declaration-btn" class="btn btn-primary" type="submit">Creer</button>
                    <button id="cancel-declaration-form-btn" class="btn btn-outline" type="button">Annuler</button>
                </div>
            </form>

            <p id="declaration-status" class="status-text"></p>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Employeur</th>
                        <th>Periode</th>
                        <th>Statut</th>
                        <th>Masse salariale</th>
                        <th>Total contribution</th>
                        <th>Lignes</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="declarations-table-body"></tbody>
                </table>
            </div>
        </article>
    @endif
</div>
@endsection

@if($canManageDeclarations)
@push('scripts')
<script>
    const csrfToken = '{{ csrf_token() }}';
    const employers = @json($employers);
    const declarationShowTemplate = @json(route('declarations.show', ['declaration' => '__ID__']));

    const state = {
        declarations: [],
    };

    const els = {
        declarationCount: document.getElementById('declaration-count'),
        declarationForm: document.getElementById('declaration-form'),
        toggleDeclarationFormBtn: document.getElementById('toggle-declaration-form-btn'),
        cancelDeclarationFormBtn: document.getElementById('cancel-declaration-form-btn'),
        saveDeclarationBtn: document.getElementById('save-declaration-btn'),
        declarationStatus: document.getElementById('declaration-status'),
        declarationsTableBody: document.getElementById('declarations-table-body'),
        employerSelect: document.getElementById('employer_id'),
    };

    function getDeclarationShowUrl(id) {
        return declarationShowTemplate.replace('__ID__', String(id));
    }

    function setDeclarationStatus(message, type = '') {
        els.declarationStatus.textContent = message;
        els.declarationStatus.className = type ? `status-text ${type}` : 'status-text';
    }

    function resetDeclarationForm() {
        const now = new Date();
        els.declarationForm.reset();
        document.getElementById('period_year').value = String(now.getFullYear());
        document.getElementById('period_month').value = String(now.getMonth() + 1);
    }

    function toggleDeclarationForm(forceVisible = null) {
        const visible = !els.declarationForm.classList.contains('is-hidden');
        const shouldShow = forceVisible === null ? !visible : forceVisible;
        els.declarationForm.classList.toggle('is-hidden', !shouldShow);
        els.toggleDeclarationFormBtn.textContent = shouldShow ? 'Fermer' : 'Nouvelle declaration';
    }

    function statusBadgeClass(status) {
        if (status === 'SUBMITTED') return 'badge badge-submitted';
        if (status === 'VALIDATED') return 'badge badge-validated';
        if (status === 'REJECTED') return 'badge badge-rejected';
        return 'badge badge-draft';
    }

    function renderDeclarations() {
        const count = state.declarations.length;
        els.declarationCount.textContent = `${count} declaration${count > 1 ? 's' : ''}`;

        if (count === 0) {
            els.declarationsTableBody.innerHTML = '<tr><td colspan="7" class="empty">Aucune declaration trouvee.</td></tr>';
            return;
        }

        els.declarationsTableBody.innerHTML = state.declarations.map((item) => `
            <tr>
                <td>${escapeHtml(item.employer_name || '-')}</td>
                <td>${escapeHtml(String(item.period_month).padStart(2, '0'))}/${escapeHtml(String(item.period_year))}</td>
                <td><span class="${statusBadgeClass(item.status)}">${escapeHtml(item.status)}</span></td>
                <td>${escapeHtml(item.total_declared_salary ?? '0')}</td>
                <td>${escapeHtml(item.total_declared_contribution ?? '0')}</td>
                <td>${escapeHtml(item.lines_count ?? 0)}</td>
                <td>
                    <div class="actions">
                        <a class="btn btn-outline" href="${escapeHtml(getDeclarationShowUrl(item.id))}">Ouvrir</a>
                        ${item.status === 'DRAFT' ? `<button class="btn btn-danger" data-action="delete" data-id="${item.id}" type="button">Supprimer</button>` : ''}
                    </div>
                </td>
            </tr>
        `).join('');
    }

    async function loadDeclarations() {
        setDeclarationStatus('Chargement des declarations...');

        try {
            const response = await fetch('/api/declarations', {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Impossible de charger les declarations.');
            }

            state.declarations = await response.json();
            renderDeclarations();
            setDeclarationStatus('Declarations chargees.', 'ok');
        } catch (error) {
            setDeclarationStatus(error.message || 'Erreur de chargement.', 'error');
        }
    }

    async function createDeclaration(event) {
        event.preventDefault();
        setDeclarationStatus('Creation en cours...');
        els.saveDeclarationBtn.disabled = true;

        try {
            const payload = {
                employer_id: Number(document.getElementById('employer_id').value),
                period_year: Number(document.getElementById('period_year').value),
                period_month: Number(document.getElementById('period_month').value),
                due_date: document.getElementById('due_date').value || null,
            };

            const response = await fetch('/api/declarations', {
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

            const created = await response.json();
            window.location.href = getDeclarationShowUrl(created.id);
        } catch (error) {
            setDeclarationStatus(error.message || 'Erreur de creation.', 'error');
        } finally {
            els.saveDeclarationBtn.disabled = false;
        }
    }

    async function deleteDeclaration(id) {
        const confirmed = confirm('Supprimer cette declaration ?');
        if (!confirmed) {
            return;
        }

        setDeclarationStatus('Suppression en cours...');

        const response = await fetch(`/api/declarations/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            setDeclarationStatus('Suppression impossible.', 'error');
            return;
        }

        await loadDeclarations();
        setDeclarationStatus('Declaration supprimee.', 'ok');
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function initEmployers() {
        els.employerSelect.innerHTML = employers
            .map((employer) => `<option value="${employer.id}">${escapeHtml(employer.legal_name)} (${escapeHtml(employer.affiliation_number)})</option>`)
            .join('');
    }

    els.toggleDeclarationFormBtn.addEventListener('click', () => toggleDeclarationForm());
    els.cancelDeclarationFormBtn.addEventListener('click', () => toggleDeclarationForm(false));
    els.declarationForm.addEventListener('submit', createDeclaration);

    els.declarationsTableBody.addEventListener('click', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const button = target.closest('button[data-action="delete"]');
        if (!button) {
            return;
        }

        const id = Number(button.dataset.id);
        await deleteDeclaration(id);
    });

    initEmployers();
    resetDeclarationForm();
    loadDeclarations();
</script>
@endpush
@endif