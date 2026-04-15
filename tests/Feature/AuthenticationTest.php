<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username(): void
    {
        User::query()->create([
            'username' => 'admin',
            'password_hash' => Hash::make('admin1234'),
            'full_name' => 'System Admin',
            'email' => 'admin@jemima.local',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'login' => 'admin',
            'password' => 'admin1234',
        ]);

        $response->assertRedirect('/');
        $this->assertAuthenticated();
    }

    public function test_authenticated_user_can_open_employeurs_page(): void
    {
        $user = User::query()->create([
            'username' => 'agent',
            'password_hash' => Hash::make('password123'),
            'full_name' => 'Agent CNSS',
            'email' => 'agent@jemima.local',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/employeurs');

        $response->assertOk();
    }
}
