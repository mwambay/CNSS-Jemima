<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()
                ->with('roles:id,code,label')
                ->orderBy('full_name')
                ->paginate(15),
            'roles' => Role::query()->orderBy('label')->get(['id', 'code', 'label']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', 'unique:users,username'],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::query()->create([
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'password_hash' => Hash::make($data['password']),
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $user->roles()->sync($data['roles']);

        return redirect()
            ->route('users.index')
            ->with('status', 'Utilisateur cree.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'max:100', Rule::unique('users', 'username')->ignore($user->id)],
            'full_name' => ['required', 'string', 'max:150'],
            'email' => ['nullable', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $newIsActive = (bool) ($data['is_active'] ?? false);
        $newRoles = Role::query()
            ->whereIn('id', $data['roles'])
            ->pluck('code')
            ->all();

        if ($this->wouldRemoveLastActiveAdmin($user, $newIsActive, $newRoles)) {
            return back()
                ->withInput()
                ->withErrors(['roles' => 'Impossible de retirer ou desactiver le dernier administrateur actif.']);
        }

        $payload = [
            'username' => $data['username'],
            'full_name' => $data['full_name'],
            'email' => $data['email'] ?? null,
            'is_active' => $newIsActive,
        ];

        if (! empty($data['password'])) {
            $payload['password_hash'] = Hash::make($data['password']);
        }

        $user->update($payload);
        $user->roles()->sync($data['roles']);

        return redirect()
            ->route('users.index')
            ->with('status', 'Utilisateur mis a jour.');
    }

    private function wouldRemoveLastActiveAdmin(User $user, bool $newIsActive, array $newRoleCodes): bool
    {
        $isCurrentlyActiveAdmin = $user->is_active
            && $user->roles()->where('code', 'ADMIN')->exists();

        if (! $isCurrentlyActiveAdmin) {
            return false;
        }

        $willStillBeActiveAdmin = $newIsActive && in_array('ADMIN', $newRoleCodes, true);

        if ($willStillBeActiveAdmin) {
            return false;
        }

        return User::query()
            ->where('id', '!=', $user->id)
            ->where('is_active', true)
            ->whereHas('roles', fn ($query) => $query->where('code', 'ADMIN'))
            ->doesntExist();
    }
}
