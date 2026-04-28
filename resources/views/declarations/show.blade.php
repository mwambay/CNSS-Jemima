@extends('layouts.app')

@section('title', 'Declaration | CNSS')
@section('page_title', 'Declaration')
@section('page_subtitle', 'Detail et lignes de declaration')

@push('styles')
<style>
    .declaration-detail-page {
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

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .65rem;
        margin-bottom: .9rem;
        flex-wrap: wrap;
    }

    .toolbar h2 {
        margin: 0;
        color: #101828;
        font-size: 1.02rem;
        font-weight: 700;
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
        justify-content: center;
        white-space: nowrap;
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

    .meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .7rem;
    }

    .meta-card {
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        padding: .75rem;
        background: #f9fafb;
    }

    .meta-label {
        display: block;
        margin-bottom: .25rem;
        color: #667085;
        font-size: .75rem;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
    }

    .meta-value {
        color: #101828;
        font-size: .95rem;
        font-weight: 700;
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
        .meta-grid,
        .grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="declaration-detail-page">
    @if(!$canManageDeclarations)
        <article class="panel">
            <div class="toolbar">
                <h2>Declaration #{{ $declaration->id }}</h2>
                <a href="{{ route('declarations.interface') }}" class="btn btn-outline">Retour a la liste</a>
            </div>
            <div class="notice">Acces restreint: vous n'avez pas les droits ADMIN pour gerer les declarations.</div>
        </article>
    @else
        <article class="panel">
            <div class="toolbar">
                <h2 id="details-title">Declaration</h2>
                <div class="toolbar-right actions">
                    <a href="{{ route('declarations.interface') }}" class="btn btn-outline">Retour a la liste</a>
                    <button id="recalculate-declaration-btn" class="btn btn-outline" type="button">Recalculer cotisations</button>
                    <button id="submit-declaration-btn" class="btn btn-primary" type="button">Soumettre</button>
                    <button id="validate-declaration-btn" class="btn btn-outline" type="button">Valider</button>
                    <button id="reject-declaration-btn" class="btn btn-danger" type="button">Rejeter</button>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-card">
                    <span class="meta-label">Employeur</span>
                    <span class="meta-value" id="meta-employer">-</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Periode</span>
                    <span class="meta-value" id="meta-period">-</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Statut</span>
                    <span class="meta-value"><span id="meta-status" class="badge badge-draft">-</span></span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Echeance</span>
                    <span class="meta-value" id="meta-due-date">-</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Masse salariale</span>
                    <span class="meta-value" id="meta-total-salary">0</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Total contribution</span>
                    <span class="meta-value" id="meta-total-contribution">0</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Lignes</span>
                    <span class="meta-value" id="meta-lines-count">0</span>
                </div>
                <div class="meta-card">
                    <span class="meta-label">Dernier message</span>
                    <span class="meta-value" id="meta-validation-message">-</span>
                </div>
            </div>
            <p id="declaration-status" class="status-text"></p>
        </article>

        <article class="panel">
            <div class="toolbar">
                <h2>Lignes de declaration</h2>
                <div class="toolbar-right">
                    <button id="toggle-line-form-btn" class="btn btn-primary" type="button">Ajouter ligne</button>
                </div>
            </div>

            <div id="line-form-block" class="is-hidden">
                <div class="grid">
                    <div class="field">
                        <label for="line_worker_id">Travailleur</label>
                        <select class="control" id="line_worker_id" name="line_worker_id" required></select>
                    </div>
                    <div class="field">
                        <label for="line_gross_salary">Salaire brut</label>
                        <input class="control" id="line_gross_salary" type="number" min="0" step="0.01" required>
                    </div>
                    <div class="field">
                        <label for="line_contributable_salary">Salaire cotisable</label>
                        <input class="control" id="line_contributable_salary" type="number" min="0" step="0.01" required>
                    </div>
                    <div class="field">
                        <label for="line_worked_days">Jours travailles</label>
                        <input class="control" id="line_worked_days" type="number" min="0" max="31">
                    </div>
                    <div class="field full">
                        <label for="line_anomaly_reason">Motif anomalie (optionnel)</label>
                        <input class="control" id="line_anomaly_reason" maxlength="255">
                    </div>
                </div>

                <div class="actions" style="margin-top:.8rem;">
                    <button id="save-line-btn" class="btn btn-primary" type="button">Ajouter / Mettre a jour ligne</button>
                    <button id="cancel-line-form-btn" class="btn btn-outline" type="button">Annuler</button>
                </div>
            </div>

            <p id="line-status" class="status-text"></p>

            <div id="lines-table-block" class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Travailleur</th>
                        <th>Numero SS</th>
                        <th>Brut</th>
                        <th>Cotisable</th>
                        <th>Part employeur</th>
                        <th>Part travailleur</th>
                        <th>Total cotisation</th>
                        <th>Jours</th>
                        <th>Anomalie</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody id="lines-table-body"></tbody>
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
    const declarationId = Number(@json($declaration->id));

    const state = {
        declaration: null,
    };

    const els = {
        detailsTitle: document.getElementById('details-title'),
        declarationStatus: document.getElementById('declaration-status'),
        metaEmployer: document.getElementById('meta-employer'),
        metaPeriod: document.getElementById('meta-period'),
        metaStatus: document.getElementById('meta-status'),
        metaDueDate: document.getElementById('meta-due-date'),
        metaTotalSalary: document.getElementById('meta-total-salary'),
        metaTotalContribution: document.getElementById('meta-total-contribution'),
        metaLinesCount: document.getElementById('meta-lines-count'),
        metaValidationMessage: document.getElementById('meta-validation-message'),
        lineWorkerSelect: document.getElementById('line_worker_id'),
        lineGrossSalary: document.getElementById('line_gross_salary'),
        lineContributableSalary: document.getElementById('line_contributable_salary'),
        lineWorkedDays: document.getElementById('line_worked_days'),
        lineAnomalyReason: document.getElementById('line_anomaly_reason'),
        lineFormBlock: document.getElementById('line-form-block'),
        linesTableBlock: document.getElementById('lines-table-block'),
        toggleLineFormBtn: document.getElementById('toggle-line-form-btn'),
        cancelLineFormBtn: document.getElementById('cancel-line-form-btn'),
        saveLineBtn: document.getElementById('save-line-btn'),
        lineStatus: document.getElementById('line-status'),
        linesTableBody: document.getElementById('lines-table-body'),
        recalculateDeclarationBtn: document.getElementById('recalculate-declaration-btn'),
        submitDeclarationBtn: document.getElementById('submit-declaration-btn'),
        validateDeclarationBtn: document.getElementById('validate-declaration-btn'),
        rejectDeclarationBtn: document.getElementById('reject-declaration-btn'),
    };

    function setStatus(target, message, type = '') {
        target.textContent = message;
        target.className = type ? `status-text ${type}` : 'status-text';
    }

    function setDeclarationStatus(message, type = '') {
        setStatus(els.declarationStatus, message, type);
    }

    function setLineStatus(message, type = '') {
        setStatus(els.lineStatus, message, type);
    }

    function escapeHtml(value) {
        return String(value)
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function statusBadgeClass(status) {
        if (status === 'SUBMITTED') return 'badge badge-submitted';
        if (status === 'VALIDATED') return 'badge badge-validated';
        if (status === 'REJECTED') return 'badge badge-rejected';
        return 'badge badge-draft';
    }

    function renderDeclaration() {
        const declaration = state.declaration;
        if (!declaration) {
            return;
        }

        els.detailsTitle.textContent = `Declaration ${String(declaration.period_month).padStart(2, '0')}/${declaration.period_year} - ${declaration.employer_name || '-'}`;
        els.metaEmployer.textContent = declaration.employer_name || '-';
        els.metaPeriod.textContent = `${String(declaration.period_month).padStart(2, '0')}/${declaration.period_year}`;
        els.metaStatus.className = statusBadgeClass(declaration.status);
        els.metaStatus.textContent = declaration.status || '-';
        els.metaDueDate.textContent = declaration.due_date || '-';
        els.metaTotalSalary.textContent = declaration.total_declared_salary ?? '0';
        els.metaTotalContribution.textContent = declaration.total_declared_contribution ?? '0';
        els.metaLinesCount.textContent = String((declaration.lines || []).length);
        els.metaValidationMessage.textContent = declaration.validation_message || '-';
    }

    function renderLines() {
        if (!state.declaration) {
            els.linesTableBody.innerHTML = '<tr><td colspan="10" class="empty">Impossible de charger les lignes.</td></tr>';
            return;
        }

        const isDraft = state.declaration.status === 'DRAFT';
        const lines = state.declaration.lines || [];

        if (lines.length === 0) {
            els.linesTableBody.innerHTML = '<tr><td colspan="10" class="empty">Aucune ligne dans cette declaration.</td></tr>';
            return;
        }

        els.linesTableBody.innerHTML = lines.map((line) => `
            <tr>
                <td>${escapeHtml(line.worker_name || '-')}</td>
                <td>${escapeHtml(line.worker_ssn || '-')}</td>
                <td>${escapeHtml(line.gross_salary ?? '0')}</td>
                <td>${escapeHtml(line.contributable_salary ?? '0')}</td>
                <td>${escapeHtml(line.employer_amount ?? '-')}</td>
                <td>${escapeHtml(line.worker_amount ?? '-')}</td>
                <td>${escapeHtml(line.total_contribution ?? '-')}</td>
                <td>${escapeHtml(line.worked_days ?? '-')}</td>
                <td>${line.anomaly_flag ? escapeHtml(line.anomaly_reason || 'Oui') : '-'}</td>
                <td>
                    ${isDraft ? `<button class="btn btn-danger" data-action="line-delete" data-id="${line.id}" type="button">Supprimer</button>` : '-'}
                </td>
            </tr>
        `).join('');
    }

    function showLineForm() {
        els.lineFormBlock.classList.remove('is-hidden');
        els.linesTableBlock.classList.add('is-hidden');
        els.toggleLineFormBtn.textContent = 'Voir lignes';
    }

    function showLinesTable() {
        els.lineFormBlock.classList.add('is-hidden');
        els.linesTableBlock.classList.remove('is-hidden');
        els.toggleLineFormBtn.textContent = 'Ajouter ligne';
    }

    function clearLineForm() {
        els.lineGrossSalary.value = '';
        els.lineContributableSalary.value = '';
        els.lineWorkedDays.value = '';
        els.lineAnomalyReason.value = '';
    }

    function updateWorkflowButtons() {
        const status = state.declaration?.status;
        const isDraft = status === 'DRAFT';
        const isSubmitted = status === 'SUBMITTED';

        els.submitDeclarationBtn.disabled = !isDraft;
        els.recalculateDeclarationBtn.disabled = !isDraft;
        els.saveLineBtn.disabled = !isDraft;
        els.toggleLineFormBtn.disabled = !isDraft;
        els.lineWorkerSelect.disabled = !isDraft;
        els.lineGrossSalary.disabled = !isDraft;
        els.lineContributableSalary.disabled = !isDraft;
        els.lineWorkedDays.disabled = !isDraft;
        els.lineAnomalyReason.disabled = !isDraft;
        els.validateDeclarationBtn.disabled = !isSubmitted;
        els.rejectDeclarationBtn.disabled = !isSubmitted;

        if (!isDraft) {
            showLinesTable();
        }
    }

    async function loadWorkersForSelectedEmployer() {
        if (!state.declaration) {
            return;
        }

        const response = await fetch(`/api/workers?employer_id=${state.declaration.employer_id}`);
        if (!response.ok) {
            throw new Error('Impossible de charger les travailleurs de cet employeur.');
        }

        const workers = await response.json();
        if (workers.length === 0) {
            els.lineWorkerSelect.innerHTML = '<option value="">Aucun travailleur actif</option>';
            return;
        }

        els.lineWorkerSelect.innerHTML = workers.map((worker) => {
            const label = `${worker.first_name || ''} ${worker.last_name || ''}`.trim();
            return `<option value="${worker.id}">${escapeHtml(label || '-')} (${escapeHtml(worker.social_security_number || '-')})</option>`;
        }).join('');
    }

    async function loadDeclaration() {
        setDeclarationStatus('Chargement de la declaration...');

        const response = await fetch(`/api/declarations/${declarationId}`, {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Impossible de charger cette declaration.');
        }

        state.declaration = await response.json();

        await loadWorkersForSelectedEmployer();
        renderDeclaration();
        renderLines();
        updateWorkflowButtons();

        setDeclarationStatus('Declaration chargee.', 'ok');
    }

    async function saveLine() {
        if (!state.declaration) {
            return;
        }

        setLineStatus('Enregistrement de la ligne...');
        els.saveLineBtn.disabled = true;

        try {
            const anomalyReason = String(els.lineAnomalyReason.value || '').trim();
            const payload = {
                worker_id: Number(els.lineWorkerSelect.value),
                gross_salary: Number(els.lineGrossSalary.value),
                contributable_salary: Number(els.lineContributableSalary.value),
                worked_days: els.lineWorkedDays.value ? Number(els.lineWorkedDays.value) : null,
                anomaly_flag: anomalyReason !== '',
                anomaly_reason: anomalyReason || null,
            };

            const response = await fetch(`/api/declarations/${state.declaration.id}/lines`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify(payload),
            });

            if (!response.ok) {
                const message = response.status === 422 ? (await response.text()) : 'Echec enregistrement ligne.';
                throw new Error(message || 'Echec enregistrement ligne.');
            }

            state.declaration = await response.json();
            renderDeclaration();
            renderLines();
            updateWorkflowButtons();
            clearLineForm();
            showLinesTable();
            setLineStatus('Ligne enregistree.', 'ok');
        } catch (error) {
            setLineStatus(error.message || 'Erreur enregistrement ligne.', 'error');
        } finally {
            els.saveLineBtn.disabled = false;
        }
    }

    async function deleteLine(lineId) {
        if (!state.declaration) {
            return;
        }

        const response = await fetch(`/api/declarations/${state.declaration.id}/lines/${lineId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            setLineStatus('Suppression de ligne impossible.', 'error');
            return;
        }

        state.declaration = await response.json();
        renderDeclaration();
        renderLines();
        updateWorkflowButtons();
        setLineStatus('Ligne supprimee.', 'ok');
    }

    async function changeWorkflow(action) {
        if (!state.declaration) {
            return;
        }

        const endpoint = `/api/declarations/${state.declaration.id}/${action}`;
        const payload = action === 'reject'
            ? { validation_message: 'Declaration rejetee par controle.' }
            : {};

        const response = await fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        });

        if (!response.ok) {
            setLineStatus('Action workflow impossible.', 'error');
            return;
        }

        await loadDeclaration();
        setLineStatus('Workflow mis a jour.', 'ok');
    }

    async function recalculateDeclaration() {
        if (!state.declaration) {
            return;
        }

        setLineStatus('Recalcul des cotisations en cours...');
        els.recalculateDeclarationBtn.disabled = true;

        try {
            const response = await fetch(`/api/declarations/${state.declaration.id}/recalculate`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
            });

            if (!response.ok) {
                throw new Error('Recalcul impossible pour cette declaration.');
            }

            state.declaration = await response.json();
            renderDeclaration();
            renderLines();
            updateWorkflowButtons();
            setLineStatus('Cotisations recalculees.', 'ok');
        } catch (error) {
            setLineStatus(error.message || 'Erreur lors du recalcul.', 'error');
            updateWorkflowButtons();
        }
    }

    els.saveLineBtn.addEventListener('click', saveLine);
    els.toggleLineFormBtn.addEventListener('click', () => {
        const shouldShowForm = els.lineFormBlock.classList.contains('is-hidden');
        if (shouldShowForm) {
            showLineForm();
            return;
        }

        showLinesTable();
    });
    els.cancelLineFormBtn.addEventListener('click', () => {
        clearLineForm();
        showLinesTable();
        setLineStatus('');
    });
    els.recalculateDeclarationBtn.addEventListener('click', recalculateDeclaration);
    els.submitDeclarationBtn.addEventListener('click', () => changeWorkflow('submit'));
    els.validateDeclarationBtn.addEventListener('click', () => changeWorkflow('validate'));
    els.rejectDeclarationBtn.addEventListener('click', () => changeWorkflow('reject'));

    els.linesTableBody.addEventListener('click', async (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const button = target.closest('button[data-action="line-delete"]');
        if (!button) {
            return;
        }

        const lineId = Number(button.dataset.id);
        await deleteLine(lineId);
    });

    showLinesTable();
    loadDeclaration().catch((error) => {
        setDeclarationStatus(error.message || 'Erreur de chargement.', 'error');
    });
</script>
@endpush
@endif
