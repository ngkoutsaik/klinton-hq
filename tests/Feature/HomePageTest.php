<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private const string ADMIN_EMAIL = 'test@example.com';

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        config(['admin.email' => self::ADMIN_EMAIL]);
    }

    public function test_it_shows_the_owners_published_resume(): void
    {
        $owner = User::factory()->create([
            'email' => self::ADMIN_EMAIL,
            'first_name' => 'Jane',
            'last_name' => 'Doe',
        ]);
        Resume::factory()->published()->for($owner)->create([
            'intro' => '<p>Backend developer who likes tidy code.</p>',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertViewIs('home');
        $response->assertSeeText('Jane Doe');
        $response->assertSee('<p>Backend developer who likes tidy code.</p>', escape: false);
    }

    public function test_it_does_not_show_an_unpublished_resume(): void
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);
        Resume::factory()->for($owner)->create();

        $this->get('/')->assertOk()->assertViewIs('coming-soon');
    }

    public function test_it_does_not_show_another_users_resume(): void
    {
        User::factory()->create(['email' => self::ADMIN_EMAIL]);
        Resume::factory()->published()->create();

        $this->get('/')->assertOk()->assertViewIs('coming-soon');
    }

    public function test_it_shows_coming_soon_when_there_is_no_resume(): void
    {
        $this->get('/')->assertOk()->assertViewIs('coming-soon');
    }
}
