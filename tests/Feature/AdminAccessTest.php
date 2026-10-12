<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_admin_can_access_the_panel(): void
    {
        $this->actingAs(User::factory()->admin()->withAppAuthentication()->create())
            ->get('/admin')
            ->assertOk();
    }

    public function test_an_admin_without_an_authenticator_app_must_set_one_up(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get('/admin')
            ->assertRedirect('/admin/multi-factor-authentication/set-up');
    }

    public function test_an_admin_can_open_their_profile(): void
    {
        $this->actingAs(User::factory()->admin()->withAppAuthentication()->create())
            ->get('/admin/profile')
            ->assertOk();
    }

    public function test_the_login_page_is_not_indexed_by_search_engines(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');
    }

    public function test_a_regular_user_cannot_access_the_panel(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    public function test_the_command_creates_an_admin(): void
    {
        $this->artisan('app:create-admin')
            ->expectsQuestion('Email', 'jane@example.com')
            ->expectsQuestion('First name', 'Jane')
            ->expectsQuestion('Last name', 'Doe')
            ->expectsQuestion('Password', 'a-long-enough-password')
            ->assertSuccessful();

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertTrue($user->is_admin);
        $this->assertSame('Jane Doe', $user->name);
    }

    public function test_the_command_promotes_an_existing_user(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->artisan('app:create-admin')
            ->expectsQuestion('Email', 'jane@example.com')
            ->expectsQuestion('First name', 'Jane')
            ->expectsQuestion('Last name', 'Doe')
            ->expectsQuestion('Password', 'a-long-enough-password')
            ->assertSuccessful();

        $this->assertSame(1, User::count());
        $this->assertTrue(User::firstOrFail()->is_admin);
    }
}
