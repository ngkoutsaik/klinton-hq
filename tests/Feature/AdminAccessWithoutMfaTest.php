<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessWithoutMfaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        // The panel registers its MFA routes and middleware on boot, so the flag must be off before the app boots.
        $this->setMfaEnabled('false');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->setMfaEnabled('true');
    }

    public function test_an_admin_without_an_authenticator_app_can_access_the_panel(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertOk();
    }

    private function setMfaEnabled(string $value): void
    {
        $_ENV['ADMIN_MFA_ENABLED'] = $value;
        putenv("ADMIN_MFA_ENABLED={$value}");
    }
}
