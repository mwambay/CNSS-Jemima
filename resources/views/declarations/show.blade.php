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
        box-shadow: 0 1px 3px rgba(6, 52, 109, 0.1), 0 1px 2px rgba(6, 52, 109, 0.06);
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
        border-color: #008f83;
        box-shadow: 0 0 0 4px rgba(0, 143, 131, 0.13);
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

    .global-dialog { width: min(520px, calc(100% - 2rem)); border: 0; border-radius: 8px; padding: 0; color: #101828; box-shadow: 0 24px 60px rgba(6, 52, 109, .24); }
    .global-dialog::backdrop { background: rgba(6, 32, 66, .58); }
    .modal-shell { padding: 1rem; }
    .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding-bottom: .8rem; margin-bottom: .9rem; border-bottom: 1px solid #e7eef2; }
    .modal-head h2 { margin: 0; color: #06346d; font-size: 1.05rem; }
    .modal-copy { margin: 0 0 .85rem; color: #667085; font-size: .88rem; line-height: 1.45; }
    .modal-actions { display: flex; justify-content: flex-end; gap: .55rem; margin-top: 1rem; }
    .mode-global { color: #006f66; background: #e4f7f3; }

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
                    <button id="record-global-contribution-btn" class="btn btn-primary" type="button">Enregistrer le montant declare</button>
                    <button id="use-detailed-entry-btn" class="btn btn-outline is-hidden" type="button">Revenir au detail par travailleur</button>
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
                    <span class="meta-label">Mode de saisie</span>
                    <span class="meta-value"><span id="meta-entry-mode" class="badge badge-draft">DETAILLE</span></span>
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

            <div id="global-mode-notice" class="notice is-hidden">Le montant global est actif. Les lignes par travailleur sont conservees mais ne participent pas au total declare.</div>

            <div id="lines-table-block" class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Travailleur</th>
                        <th>Matricule</th>
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

        <dialog class="global-dialog" id="global-contribution-dialog">
            <div class="modal-shell">
                <div class="modal-head">
                    <h2>Montant global declare</h2>
                    <button id="close-global-dialog" class="btn btn-outline" type="button">Fermer</button>
                </div>
                <p class="modal-copy">Ce montant devient le total declare a la CNSS pour cette periode. Les lignes par travailleur restent conservees mais sont ignorees tant que le mode global est actif.</p>
                <div class="field">
                    <label for="global_contribution_amount">Montant declare</label>
                    <input class="control" id="global_contribution_amount" type="number" min="0" step="0.01" required>
                </div>
                <p id="global-contribution-status" class="status-text"></p>
                <div class="modal-actions">
                    <button id="cancel-global-contribution" class="btn btn-outline" type="button">Annuler</button>
                    <button id="save-global-contribution" class="btn btn-primary" type="button">Enregistrer le montant</button>
                </div>
            </div>
        </dialog>
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
        metaEntryMode: document.getElementById('meta-entry-mode'),
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
        recordGlobalContributionBtn: document.getElementById('record-global-contribution-btn'),
        useDetailedEntryBtn: document.getElementById('use-detailed-entry-btn'),
        globalContributionDialog: document.getElementById('global-contribution-dialog'),
        globalContributionAmount: document.getElementById('global_contribution_amount'),
        globalContributionStatus: document.getElementById('global-contribution-status'),
        closeGlobalDialog: document.getElementById('close-global-dialog'),
        cancelGlobalContribution: document.getElementById('cancel-global-contribution'),
        saveGlobalContribution: document.getElementById('save-global-contribution'),
        globalModeNotice: document.getElementById('global-mode-notice'),
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
        const isGlobal = declaration.contribution_entry_mode === 'GLOBAL';
        els.metaEntryMode.className = `badge ${isGlobal ? 'mode-global' : 'badge-draft'}`;
        els.metaEntryMode.textContent = isGlobal ? 'GLOBAL' : 'DETAILLE';
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
        const isGlobal = state.declaration.contribution_entry_mode === 'GLOBAL';
        const lines = state.declaration.lines || [];

        els.globalModeNotice.classList.toggle('is-hidden', !isGlobal);

        if (isGlobal) {
            els.linesTableBody.innerHTML = `<tr><td colspan="10" class="empty">Montant global declare: ${escapeHtml(state.declaration.global_contribution_amount ?? '0')}</td></tr>`;
            return;
        }

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
        const isGlobal = state.declaration?.contribution_entry_mode === 'GLOBAL';

        els.submitDeclarationBtn.disabled = !isDraft;
        els.recordGlobalContributionBtn.disabled = !isDraft;
        els.recordGlobalContributionBtn.textContent = isGlobal ? 'Modifier le montant declare' : 'Enregistrer le montant declare';
        els.useDetailedEntryBtn.classList.toggle('is-hidden', !isDraft || !isGlobal);
        els.recalculateDeclarationBtn.disabled = !isDraft || isGlobal;
        els.saveLineBtn.disabled = !isDraft || isGlobal;
        els.toggleLineFormBtn.disabled = !isDraft || isGlobal;
        els.lineWorkerSelect.disabled = !isDraft || isGlobal;
        els.lineGrossSalary.disabled = !isDraft || isGlobal;
        els.lineContributableSalary.disabled = !isDraft || isGlobal;
        els.lineWorkedDays.disabled = !isDraft || isGlobal;
        els.lineAnomalyReason.disabled = !isDraft || isGlobal;
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

    function openGlobalContributionDialog() {
        if (!state.declaration) {
            return;
        }

        els.globalContributionAmount.value = state.declaration.global_contribution_amount ?? state.declaration.total_declared_contribution ?? '';
        setStatus(els.globalContributionStatus, '');
        els.globalContributionDialog.showModal();
    }

    async function saveGlobalContribution() {
        if (!state.declaration) {
            return;
        }

        setStatus(els.globalContributionStatus, 'Enregistrement en cours...');
        els.saveGlobalContribution.disabled = true;

        try {
            const response = await fetch(`/api/declarations/${state.declaration.id}/global-contribution`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ amount: els.globalContributionAmount.value }),
            });

            if (!response.ok) {
                const data = await response.json().catch(() => ({}));
                const message = Object.values(data.errors || {}).flat().join(' ');
                throw new Error(message || 'Enregistrement du montant impossible.');
            }

            state.declaration = await response.json();
            els.globalContributionDialog.close();
            renderDeclaration();
            renderLines();
            updateWorkflowButtons();
            showLinesTable();
            setDeclarationStatus('Montant global enregistre.', 'ok');
        } catch (error) {
            setStatus(els.globalContributionStatus, error.message || 'Erreur d enregistrement.', 'error');
        } finally {
            els.saveGlobalContribution.disabled = false;
        }
    }

    async function useDetailedEntry() {
        if (!state.declaration) {
            return;
        }

        const response = await fetch(`/api/declarations/${state.declaration.id}/use-detailed-entry`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });

        if (!response.ok) {
            setDeclarationStatus('Retour au mode detaille impossible.', 'error');
            return;
        }

        state.declaration = await response.json();
        renderDeclaration();
        renderLines();
        updateWorkflowButtons();
        setDeclarationStatus('Mode detaille active.', 'ok');
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
    els.recordGlobalContributionBtn.addEventListener('click', openGlobalContributionDialog);
    els.closeGlobalDialog.addEventListener('click', () => els.globalContributionDialog.close());
    els.cancelGlobalContribution.addEventListener('click', () => els.globalContributionDialog.close());
    els.saveGlobalContribution.addEventListener('click', saveGlobalContribution);
    els.useDetailedEntryBtn.addEventListener('click', useDetailedEntry);

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
