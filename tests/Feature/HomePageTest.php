<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    private const int OWNER_ID = 1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_it_shows_the_owners_published_resume(): void
    {
        $owner = User::factory()->create([
            'id' => self::OWNER_ID,
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
        $owner = User::factory()->create(['id' => self::OWNER_ID]);
        Resume::factory()->for($owner)->create();

        $this->get('/')->assertNotFound();
    }

    public function test_it_does_not_show_another_users_resume(): void
    {
        User::factory()->create(['id' => self::OWNER_ID]);
        Resume::factory()->published()->create();

        $this->get('/')->assertNotFound();
    }

    public function test_it_returns_not_found_when_there_is_no_resume(): void
    {
        $this->get('/')->assertNotFound();
    }
}
