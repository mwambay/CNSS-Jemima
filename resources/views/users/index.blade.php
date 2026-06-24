@extends('layouts.app')

@section('title', 'Utilisateurs | CNSS')
@section('page_title', 'Gestion des utilisateurs')
@section('page_subtitle', 'Comptes applicatifs et roles')

@push('styles')
<style>
    .panel { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 1px 3px rgba(6, 52, 109, .08); overflow: hidden; }
    .panel-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e4e7ec; }
    .panel-head h2 { margin: 0; font-size: 1rem; color: #101828; }
    .panel-head p { margin: .2rem 0 0; color: #667085; font-size: .82rem; }
    .alert { margin-bottom: 1rem; padding: .8rem 1rem; border: 1px solid #99e7dd; background: #f1fffc; color: #006f66; border-radius: 8px; font-weight: 700; }
    .errors { margin-bottom: 1rem; padding: .8rem 1rem; border: 1px solid #fecdca; background: #fff6f5; color: #b42318; border-radius: 8px; }
    .table-wrap { overflow-x: auto; }
    table { width: 100%; min-width: 860px; border-collapse: collapse; }
    th, td { padding: .82rem 1rem; border-bottom: 1px solid #e4e7ec; text-align: left; font-size: .84rem; vertical-align: top; }
    th { color: #667085; background: #f9fafb; text-transform: uppercase; font-size: .7rem; }
    td strong { color: #101828; }
    .muted { color: #667085; font-size: .78rem; }
    .badge { display: inline-flex; align-items: center; border-radius: 999px; padding: .22rem .5rem; margin: .12rem; font-size: .68rem; font-weight: 800; background: #e4f7f3; color: #006f66; }
    .badge.off { background: #f2f4f7; color: #667085; }
    .btn-primary { background: #06346d; color: #fff; }
    .btn-primary:hover { background: #052b5b; }
    .btn-light { background: #fff; color: #344054; border: 1px solid #d0d5dd; }
    .modal-backdrop { position: fixed; inset: 0; display: none; place-items: center; padding: 1rem; background: rgba(16, 24, 40, .42); z-index: 20; }
    .modal-backdrop.is-open { display: grid; }
    .modal { width: min(720px, 100%); max-height: 92vh; overflow: auto; background: #fff; border-radius: 8px; box-shadow: 0 20px 45px rgba(16, 24, 40, .22); }
    .modal-head { display: flex; justify-content: space-between; align-items: center; gap: 1rem; padding: 1rem; border-bottom: 1px solid #e4e7ec; }
    .modal-head h2 { margin: 0; font-size: 1rem; }
    .modal-body { display: grid; gap: .9rem; padding: 1rem; }
    .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .8rem; }
    label { display: grid; gap: .32rem; color: #344054; font-size: .82rem; font-weight: 700; }
    .control { width: 100%; border: 1px solid #d0d5dd; border-radius: 8px; padding: .58rem .7rem; font: inherit; color: #101828; background: #fff; }
    .check-row { display: flex; align-items: center; gap: .45rem; font-size: .84rem; color: #344054; }
    .role-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .55rem; padding: .75rem; border: 1px solid #d0d5dd; border-radius: 8px; }
    .modal-actions { display: flex; justify-content: flex-end; gap: .65rem; padding: 1rem; border-top: 1px solid #e4e7ec; }
    .pagination { padding: 1rem; }
    @media (max-width: 680px) {
        .grid, .role-grid { grid-template-columns: 1fr; }
        .panel-head { align-items: flex-start; flex-direction: column; }
    }
</style>
@endpush

@section('content')
@if(session('status'))
    <div class="alert">{{ session('status') }}</div>
@endif

@if($errors->any())
    <div class="errors">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<section class="panel">
    <div class="panel-head">
        <div>
            <h2>Utilisateurs</h2>
            <p>Creer les comptes, affecter les roles et suspendre les acces.</p>
        </div>
        <button class="btn btn-primary" type="button" data-open-create>Ajouter un utilisateur</button>
    </div>

    <div class="table-wrap">
        <table>
            <thead>
            <tr>
                <th>Utilisateur</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            @forelse($users as $user)
                <tr>
                    <td>
                        <strong>{{ $user->full_name }}</strong>
                        <div class="muted">{{ $user->username }}</div>
                    </td>
                    <td>{{ $user->email ?? '-' }}</td>
                    <td>
                        @foreach($user->roles as $role)
                            <span class="badge">{{ $role->label }}</span>
                        @endforeach
                    </td>
                    <td>
                        <span class="badge {{ $user->is_active ? '' : 'off' }}">{{ $user->is_active ? 'ACTIF' : 'INACTIF' }}</span>
                    </td>
                    <td>
                        <button
                            class="btn btn-light"
                            type="button"
                            data-open-edit
                            data-action="{{ route('users.update', $user) }}"
                            data-username="{{ $user->username }}"
                            data-full-name="{{ $user->full_name }}"
                            data-email="{{ $user->email }}"
                            data-active="{{ $user->is_active ? '1' : '0' }}"
                            data-roles="{{ $user->roles->pluck('id')->implode(',') }}"
                        >Modifier</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun utilisateur.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination">{{ $users->links() }}</div>
</section>

<div class="modal-backdrop" id="user-modal" aria-hidden="true">
    <form class="modal" id="user-form" method="POST" action="{{ route('users.store') }}">
        @csrf
        <input type="hidden" name="_method" id="form-method" value="POST" disabled>
        <div class="modal-head">
            <h2 id="modal-title">Ajouter un utilisateur</h2>
            <button class="btn btn-light" type="button" data-close-modal>Fermer</button>
        </div>
        <div class="modal-body">
            <div class="grid">
                <label>Nom complet
                    <input class="control" id="full_name" name="full_name" maxlength="150" required>
                </label>
                <label>Nom utilisateur
                    <input class="control" id="username" name="username" maxlength="100" required>
                </label>
                <label>Email
                    <input class="control" id="email" name="email" type="email" maxlength="150">
                </label>
                <label>Mot de passe
                    <input class="control" id="password" name="password" type="password" minlength="8" autocomplete="new-password">
                    <span class="muted" id="password-note">Minimum 8 caracteres.</span>
                </label>
            </div>

            <label class="check-row">
                <input id="is_active" name="is_active" type="checkbox" value="1" checked>
                Compte actif
            </label>

            <div>
                <div class="muted" style="margin-bottom:.35rem;font-weight:700;">Roles</div>
                <div class="role-grid">
                    @foreach($roles as $role)
                        <label class="check-row">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}">
                            {{ $role->label }} <span class="muted">({{ $role->code }})</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn btn-light" type="button" data-close-modal>Annuler</button>
            <button class="btn btn-primary" type="submit">Enregistrer</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    const modal = document.getElementById('user-modal');
    const form = document.getElementById('user-form');
    const method = document.getElementById('form-method');
    const modalTitle = document.getElementById('modal-title');
    const password = document.getElementById('password');
    const passwordNote = document.getElementById('password-note');
    const roleInputs = [...form.querySelectorAll('input[name="roles[]"]')];

    function openModal() {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
    }

    function resetFormForCreate() {
        form.reset();
        form.action = @json(route('users.store'));
        method.disabled = true;
        method.value = 'POST';
        modalTitle.textContent = 'Ajouter un utilisateur';
        password.required = true;
        passwordNote.textContent = 'Minimum 8 caracteres.';
        document.getElementById('is_active').checked = true;
    }

    document.querySelector('[data-open-create]').addEventListener('click', () => {
        resetFormForCreate();
        openModal();
    });

    document.querySelectorAll('[data-open-edit]').forEach((button) => {
        button.addEventListener('click', () => {
            form.reset();
            form.action = button.dataset.action;
            method.disabled = false;
            method.value = 'PUT';
            modalTitle.textContent = 'Modifier un utilisateur';
            document.getElementById('username').value = button.dataset.username || '';
            document.getElementById('full_name').value = button.dataset.fullName || '';
            document.getElementById('email').value = button.dataset.email || '';
            document.getElementById('is_active').checked = button.dataset.active === '1';
            password.required = false;
            password.value = '';
            passwordNote.textContent = 'Laisser vide pour conserver le mot de passe actuel.';

            const selectedRoles = (button.dataset.roles || '').split(',').filter(Boolean);
            roleInputs.forEach((input) => {
                input.checked = selectedRoles.includes(input.value);
            });
            openModal();
        });
    });

    document.querySelectorAll('[data-close-modal]').forEach((button) => {
        button.addEventListener('click', closeModal);
    });

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
</script>
@endpush
