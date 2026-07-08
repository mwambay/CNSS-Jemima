<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_guest_can_open_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Déclarations et affiliations employeurs')
            ->assertSee('Demander une affiliation')
            ->assertSee('Se connecter');
    }

    public function test_guest_is_redirected_to_login_from_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
