<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_users_page(): void
    {
        $admin = $this->userWithRole('ADMIN');
        Role::query()->create(['code' => 'SDT', 'label' => 'Service de traitement']);

        $this->actingAs($admin)
            ->get('/utilisateurs')
            ->assertOk()
            ->assertSee('Gestion des utilisateurs')
            ->assertSee('Service de traitement');
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $agent = $this->userWithRole('AGENT_SES');

        $this->actingAs($agent)
            ->get('/utilisateurs')
            ->assertForbidden();
    }

    public function test_admin_can_create_user_with_sdt_role(): void
    {
        $admin = $this->userWithRole('ADMIN');
        $sdt = Role::query()->create(['code' => 'SDT', 'label' => 'Service de traitement']);

        $this->actingAs($admin)
            ->post('/utilisateurs', [
                'username' => 'sdt_user',
                'full_name' => 'Agent SDT',
                'email' => 'sdt@jemima.local',
                'password' => 'password123',
                'is_active' => '1',
                'roles' => [$sdt->id],
            ])
            ->assertRedirect('/utilisateurs');

        $user = User::query()->where('username', 'sdt_user')->firstOrFail();

        $this->assertTrue(Hash::check('password123', $user->password_hash));
        $this->assertTrue($user->roles()->where('code', 'SDT')->exists());
    }

    public function test_admin_can_update_user_roles_and_status(): void
    {
        $admin = $this->userWithRole('ADMIN');
        $sdt = Role::query()->create(['code' => 'SDT', 'label' => 'Service de traitement']);
        $user = $this->plainUser('worker_admin');

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'username' => 'worker_admin',
                'full_name' => 'Worker Admin',
                'email' => 'worker@jemima.local',
                'is_active' => '0',
                'roles' => [$sdt->id],
            ])
            ->assertRedirect('/utilisateurs');

        $user->refresh();

        $this->assertFalse($user->is_active);
        $this->assertTrue($user->roles()->where('code', 'SDT')->exists());
    }

    public function test_cannot_disable_last_active_admin(): void
    {
        $admin = $this->userWithRole('ADMIN');
        $sdt = Role::query()->create(['code' => 'SDT', 'label' => 'Service de traitement']);

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'username' => $admin->username,
                'full_name' => $admin->full_name,
                'email' => $admin->email,
                'is_active' => '0',
                'roles' => [$sdt->id],
            ])
            ->assertSessionHasErrors('roles');
    }

    private function userWithRole(string $roleCode): User
    {
        $role = Role::query()->create([
            'code' => $roleCode,
            'label' => $roleCode,
        ]);

        $user = $this->plainUser(strtolower($roleCode));
        $user->roles()->attach($role->id);

        return $user;
    }

    private function plainUser(string $username): User
    {
        return User::query()->create([
            'username' => $username,
            'password_hash' => Hash::make('password123'),
            'full_name' => ucfirst($username).' User',
            'email' => $username.'@jemima.local',
            'is_active' => true,
        ]);
    }
}
