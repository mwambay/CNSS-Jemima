@extends('layouts.app')

@section('title', 'Déclarations | CNSS')
@section('page_title', 'Déclarations')
@section('page_subtitle', 'Gestion des déclarations mensuelles')

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
        box-shadow: 0 1px 3px rgba(6, 52, 109, 0.1), 0 1px 2px rgba(6, 52, 109, 0.06);
        padding: 1rem;
    }

    .panel h2 {
        margin: 0;
        color: #101828;
        font-size: 1.02rem;
        font-weight: 700;
    }

    .overview-panel {
        background: #fff;
        border: 1px solid #dfe5ec;
        border-radius: 8px;
        box-shadow: 0 1px 3px rgba(6, 52, 109, .08);
        padding: 1.15rem;
    }

    .overview-panel h2 {
        margin: 0 0 1rem;
        color: #101828;
        font-size: 1.05rem;
    }

    .overview-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        border: 1px solid #dfe5ec;
        border-radius: 8px;
        overflow: hidden;
    }

    .overview-item {
        min-width: 0;
        padding: 1rem 1.15rem;
        background: #fff;
        border-right: 1px solid #dfe5ec;
    }

    .overview-item:last-child {
        border-right: 0;
    }

    .overview-label {
        display: block;
        margin-bottom: .55rem;
        color: #667085;
        font-size: .82rem;
        font-weight: 600;
    }

    .overview-value-row {
        display: flex;
        align-items: center;
        gap: .55rem;
        flex-wrap: wrap;
    }

    .overview-value {
        color: #062b5c;
        font-size: 1.65rem;
        line-height: 1.15;
        font-weight: 800;
        overflow-wrap: anywhere;
    }

    .overview-note {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .22rem .5rem;
        color: #006f66;
        background: #e4f7f3;
        font-size: .72rem;
        font-weight: 800;
    }

    .overview-note.warning {
        color: #b54708;
        background: #fffaeb;
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
        color: #06346d;
        background: #e7f0fa;
        border: 1px solid #d6e8f4;
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
        border-color: #008f83;
        box-shadow: 0 0 0 4px rgba(0, 143, 131, 0.13);
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
        background: #06346d;
        color: #fff;
    }

    .btn-outline {
        background: #fff;
        color: #344054;
        border: 1px solid #d0d5dd;
    }

    .btn-danger {
        background: #e4f7f3;
        color: #006f66;
        border: 1px solid #8acdc4;
    }

    .actions {
        display: flex;
        gap: .5rem;
        flex-wrap: wrap;
    }

    .filter-bar {
        display: grid;
        grid-template-columns: minmax(220px, 1.6fr) repeat(3, minmax(130px, .7fr)) auto;
        align-items: end;
        gap: .65rem;
        margin: .9rem 0;
        padding: .8rem;
        border: 1px solid #e4e7ec;
        border-radius: 8px;
        background: #f8fafb;
    }

    .filter-field {
        display: grid;
        gap: .3rem;
        min-width: 0;
    }

    .filter-field label {
        color: #667085;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .filter-field .control {
        background: #fff;
    }

    .filter-bar > .btn {
        min-height: 42px;
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
        color: #06346d;
        background: #e7f0fa;
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

        .overview-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .overview-item:nth-child(2) {
            border-right: 0;
        }

        .overview-item:nth-child(-n + 2) {
            border-bottom: 1px solid #dfe5ec;
        }

        .filter-bar {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .filter-field-search {
            grid-column: 1 / -1;
        }
    }

    @media (max-width: 600px) {
        .overview-grid {
            grid-template-columns: 1fr;
        }

        .overview-item,
        .overview-item:nth-child(2) {
            border-right: 0;
            border-bottom: 1px solid #dfe5ec;
        }

        .overview-item:last-child {
            border-bottom: 0;
        }

        .filter-bar {
            grid-template-columns: 1fr;
        }

        .filter-field-search {
            grid-column: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="declaration-page">
    @if(!$canManageDeclarations)
        <article class="panel">
            <div class="notice">Accès restreint: vous n'avez pas les droits ADMIN pour gérer les déclarations.</div>
        </article>
    @else
        <section class="overview-panel" aria-labelledby="overview-title">
            <h2 id="overview-title">Vue d'ensemble</h2>
            <div class="overview-grid">
                <div class="overview-item">
                    <span class="overview-label">Total des déclarations</span>
                    <div class="overview-value-row">
                        <strong class="overview-value" id="overview-declarations">0</strong>
                        <span class="overview-note" id="overview-validated">0 validée</span>
                    </div>
                </div>
                <div class="overview-item">
                    <span class="overview-label">Sommes cotisées</span>
                    <div class="overview-value-row">
                        <strong class="overview-value" id="overview-contributed">0,00 CDF</strong>
                    </div>
                </div>
                <div class="overview-item">
                    <span class="overview-label">Total exigible</span>
                    <div class="overview-value-row">
                        <strong class="overview-value" id="overview-payable">0,00 CDF</strong>
                    </div>
                </div>
                <div class="overview-item">
                    <span class="overview-label">Majorations de retard</span>
                    <div class="overview-value-row">
                        <strong class="overview-value" id="overview-penalties">0,00 CDF</strong>
                        <span class="overview-note warning" id="overview-late-count">0 retard</span>
                    </div>
                </div>
            </div>
        </section>

        <article class="panel">
            <div class="toolbar">
                <h2>Liste des déclarations</h2>
                <div class="toolbar-right">
                    <span class="kpi" id="declaration-count">0 déclaration</span>
                    <button id="toggle-declaration-form-btn" class="btn btn-primary" type="button">Nouvelle déclaration</button>
                </div>
            </div>

            <form id="declaration-form" class="is-hidden">
                <div class="grid">
                    <div class="field">
                        <label for="employer_id">Employeur</label>
                        <select class="control" id="employer_id" name="employer_id" required></select>
                    </div>
                    <div class="field">
                        <label for="period_year">Année</label>
                        <input class="control" id="period_year" name="period_year" type="number" min="2000" max="2100" required>
                    </div>
                    <div class="field">
                        <label for="period_month">Mois</label>
                        <input class="control" id="period_month" name="period_month" type="number" min="1" max="12" required>
                    </div>
                    <div class="field">
                        <label for="due_date">Échéance légale (15 du mois suivant)</label>
                        <input class="control" id="due_date" name="due_date" type="date" readonly>
                    </div>
                </div>
                <div class="actions" style="margin-top:.8rem;">
                    <button id="save-declaration-btn" class="btn btn-primary" type="submit">Créer</button>
                    <button id="cancel-declaration-form-btn" class="btn btn-outline" type="button">Annuler</button>
                </div>
            </form>

            <div class="filter-bar" aria-label="Filtres des déclarations">
                <div class="filter-field filter-field-search">
                    <label for="declaration-search">Recherche</label>
                    <input class="control" id="declaration-search" type="search" placeholder="Employeur ou période (ex. 04/2026)">
                </div>
                <div class="filter-field">
                    <label for="declaration-status-filter">Statut</label>
                    <select class="control" id="declaration-status-filter">
                        <option value="">Tous les statuts</option>
                        <option value="DRAFT">Brouillon</option>
                        <option value="SUBMITTED">Soumise</option>
                        <option value="VALIDATED">Validée</option>
                        <option value="REJECTED">Rejetée</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="declaration-year-filter">Année</label>
                    <select class="control" id="declaration-year-filter">
                        <option value="">Toutes les années</option>
                    </select>
                </div>
                <div class="filter-field">
                    <label for="declaration-month-filter">Mois</label>
                    <select class="control" id="declaration-month-filter">
                        <option value="">Tous les mois</option>
                        <option value="1">Janvier</option>
                        <option value="2">Février</option>
                        <option value="3">Mars</option>
                        <option value="4">Avril</option>
                        <option value="5">Mai</option>
                        <option value="6">Juin</option>
                        <option value="7">Juillet</option>
                        <option value="8">Août</option>
                        <option value="9">Septembre</option>
                        <option value="10">Octobre</option>
                        <option value="11">Novembre</option>
                        <option value="12">Décembre</option>
                    </select>
                </div>
                <button class="btn btn-outline" id="reset-declaration-filters" type="button">Réinitialiser</button>
            </div>

            <p id="declaration-status" class="status-text"></p>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Employeur</th>
                        <th>Période</th>
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
        filteredDeclarations: [],
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
        overviewDeclarations: document.getElementById('overview-declarations'),
        overviewValidated: document.getElementById('overview-validated'),
        overviewContributed: document.getElementById('overview-contributed'),
        overviewPayable: document.getElementById('overview-payable'),
        overviewPenalties: document.getElementById('overview-penalties'),
        overviewLateCount: document.getElementById('overview-late-count'),
        searchInput: document.getElementById('declaration-search'),
        statusFilter: document.getElementById('declaration-status-filter'),
        yearFilter: document.getElementById('declaration-year-filter'),
        monthFilter: document.getElementById('declaration-month-filter'),
        resetFiltersBtn: document.getElementById('reset-declaration-filters'),
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
        updateDueDate();
    }

    function updateDueDate() {
        const year = Number(document.getElementById('period_year').value);
        const month = Number(document.getElementById('period_month').value);

        if (!year || month < 1 || month > 12) {
            document.getElementById('due_date').value = '';
            return;
        }

        const dueDate = new Date(Date.UTC(year, month, 15));
        document.getElementById('due_date').value = dueDate.toISOString().slice(0, 10);
    }

    function toggleDeclarationForm(forceVisible = null) {
        const visible = !els.declarationForm.classList.contains('is-hidden');
        const shouldShow = forceVisible === null ? !visible : forceVisible;
        els.declarationForm.classList.toggle('is-hidden', !shouldShow);
        els.toggleDeclarationFormBtn.textContent = shouldShow ? 'Fermer' : 'Nouvelle déclaration';
    }

    function statusBadgeClass(status) {
        if (status === 'SUBMITTED') return 'badge badge-submitted';
        if (status === 'VALIDATED') return 'badge badge-validated';
        if (status === 'REJECTED') return 'badge badge-rejected';
        return 'badge badge-draft';
    }

    function statusLabel(status) {
        if (status === 'SUBMITTED') return 'Soumise';
        if (status === 'VALIDATED') return 'Validée';
        if (status === 'REJECTED') return 'Rejetée';
        return 'Brouillon';
    }

    function formatCurrency(value) {
        return `${new Intl.NumberFormat('fr-FR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
        }).format(Number(value || 0))} CDF`;
    }

    function renderOverview() {
        const declarations = state.declarations;
        const validatedCount = declarations.filter((item) => item.status === 'VALIDATED').length;
        const lateCount = declarations.filter((item) => Number(item.global_late_days || 0) > 0).length;
        const totalContributed = declarations.reduce(
            (total, item) => total + Number(item.total_declared_contribution || 0),
            0
        );
        const totalPayable = declarations.reduce(
            (total, item) => total + Number(item.global_total_payable ?? item.total_declared_contribution ?? 0),
            0
        );
        const totalPenalties = declarations.reduce(
            (total, item) => total + Number(item.global_late_penalty_amount || 0),
            0
        );

        els.overviewDeclarations.textContent = String(declarations.length);
        els.overviewValidated.textContent = `${validatedCount} validée${validatedCount > 1 ? 's' : ''}`;
        els.overviewContributed.textContent = formatCurrency(totalContributed);
        els.overviewPayable.textContent = formatCurrency(totalPayable);
        els.overviewPenalties.textContent = formatCurrency(totalPenalties);
        els.overviewLateCount.textContent = `${lateCount} retard${lateCount > 1 ? 's' : ''}`;
    }

    function renderDeclarations() {
        const count = state.filteredDeclarations.length;
        const totalCount = state.declarations.length;
        els.declarationCount.textContent = count === totalCount
            ? `${count} déclaration${count > 1 ? 's' : ''}`
            : `${count} résultat${count > 1 ? 's' : ''} sur ${totalCount}`;
        renderOverview();

        if (count === 0) {
            els.declarationsTableBody.innerHTML = '<tr><td colspan="7" class="empty">Aucune déclaration trouvée.</td></tr>';
            return;
        }

        els.declarationsTableBody.innerHTML = state.filteredDeclarations.map((item) => `
            <tr>
                <td>${escapeHtml(item.employer_name || '-')}</td>
                <td>${escapeHtml(String(item.period_month).padStart(2, '0'))}/${escapeHtml(String(item.period_year))}</td>
                <td><span class="${statusBadgeClass(item.status)}">${escapeHtml(statusLabel(item.status))}</span></td>
                <td>${escapeHtml(formatCurrency(item.total_declared_salary))}</td>
                <td>${escapeHtml(formatCurrency(item.total_declared_contribution))}</td>
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

    function populateYearFilter() {
        const selectedYear = els.yearFilter.value;
        const years = [...new Set(state.declarations.map((item) => Number(item.period_year)))]
            .filter(Boolean)
            .sort((a, b) => b - a);

        els.yearFilter.innerHTML = '<option value="">Toutes les années</option>'
            + years.map((year) => `<option value="${year}">${year}</option>`).join('');
        els.yearFilter.value = years.includes(Number(selectedYear)) ? selectedYear : '';
    }

    function applyFilters() {
        const search = els.searchInput.value.trim().toLocaleLowerCase('fr');
        const status = els.statusFilter.value;
        const year = Number(els.yearFilter.value || 0);
        const month = Number(els.monthFilter.value || 0);

        state.filteredDeclarations = state.declarations.filter((item) => {
            const period = `${String(item.period_month).padStart(2, '0')}/${item.period_year}`;
            const searchableText = `${item.employer_name || ''} ${period}`.toLocaleLowerCase('fr');

            return (!search || searchableText.includes(search))
                && (!status || item.status === status)
                && (!year || Number(item.period_year) === year)
                && (!month || Number(item.period_month) === month);
        });

        renderDeclarations();
    }

    function resetFilters() {
        els.searchInput.value = '';
        els.statusFilter.value = '';
        els.yearFilter.value = '';
        els.monthFilter.value = '';
        applyFilters();
    }

    async function loadDeclarations() {
        setDeclarationStatus('Chargement des déclarations...');

        try {
            const response = await fetch('/api/declarations', {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) {
                throw new Error('Impossible de charger les déclarations.');
            }

            state.declarations = await response.json();
            state.filteredDeclarations = [...state.declarations];
            populateYearFilter();
            applyFilters();
            setDeclarationStatus('Déclarations chargées.', 'ok');
        } catch (error) {
            setDeclarationStatus(error.message || 'Erreur de chargement.', 'error');
        }
    }

    async function createDeclaration(event) {
        event.preventDefault();
        setDeclarationStatus('Création en cours...');
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
                throw new Error('Création impossible.');
            }

            const created = await response.json();
            window.location.href = getDeclarationShowUrl(created.id);
        } catch (error) {
            setDeclarationStatus(error.message || 'Erreur de création.', 'error');
        } finally {
            els.saveDeclarationBtn.disabled = false;
        }
    }

    async function deleteDeclaration(id) {
        const confirmed = confirm('Supprimer cette déclaration ?');
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
        setDeclarationStatus('Déclaration supprimée.', 'ok');
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
    document.getElementById('period_year').addEventListener('input', updateDueDate);
    document.getElementById('period_month').addEventListener('input', updateDueDate);
    els.searchInput.addEventListener('input', applyFilters);
    els.statusFilter.addEventListener('change', applyFilters);
    els.yearFilter.addEventListener('change', applyFilters);
    els.monthFilter.addEventListener('change', applyFilters);
    els.resetFiltersBtn.addEventListener('click', resetFilters);

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
