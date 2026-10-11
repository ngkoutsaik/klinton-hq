<?php

namespace Tests\Feature;

use App\Models\Resume;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_an_admin_in_the_testing_environment(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com', 'is_admin' => true]);
    }

    public function test_it_seeds_a_published_resume_for_the_admin(): void
    {
        $this->seed();

        $resume = Resume::sole();
        $this->assertTrue($resume->published);
        $this->assertSame('test@example.com', $resume->user->email);
        $this->assertCount(3, $resume->workExperiences);
        $this->assertCount(2, $resume->education);
        $this->assertCount(3, $resume->links);
        $this->assertCount(2, $resume->activeExtraInfo);
        $this->assertCount(10, $resume->skills);
    }

    public function test_the_seeded_resume_is_shown_on_the_home_page(): void
    {
        $this->withoutVite();
        config(['admin.email' => 'test@example.com']);

        $this->seed();

        $this->get('/')->assertOk()->assertViewIs('home')->assertSeeText('University of Edinburgh');
    }

    public function test_it_does_not_seed_an_admin_in_production(): void
    {
        $this->app['env'] = 'production';

        $this->artisan('db:seed', ['--force' => true])->assertSuccessful();

        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
        $this->assertDatabaseEmpty('resumes');
    }
}
