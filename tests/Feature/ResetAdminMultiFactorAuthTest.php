<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResetAdminMultiFactorAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_command_resets_an_admins_authenticator_app(): void
    {
        $user = User::factory()->admin()->withAppAuthentication()->create();

        $this->artisan('app:reset-admin-mfa', ['email' => $user->email])->assertSuccessful();

        $this->assertNull($user->refresh()->app_authentication_secret);
    }

    public function test_the_reset_command_fails_for_an_unknown_email(): void
    {
        $this->artisan('app:reset-admin-mfa', ['email' => 'nobody@example.com'])->assertFailed();
    }
}
