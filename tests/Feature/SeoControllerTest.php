<?php

namespace Tests\Feature;

use App\Models\Resume;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class SeoControllerTest extends TestCase
{
    use RefreshDatabase;

    private const string ADMIN_EMAIL = 'admin@test.com';

    protected function setUp(): void
    {
        parent::setUp();

        config(['admin.email' => self::ADMIN_EMAIL]);
    }

    public function test_the_sitemap_lists_the_home_page_with_the_resumes_last_update(): void
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);
        $resume = Resume::factory()->published()->for($owner)->create([
            'updated_at' => Carbon::parse('2026-01-02 03:04:05'),
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml');
        $response->assertSee('<loc>'.url('/').'</loc>', escape: false);
        $response->assertSee('<lastmod>'.$resume->updated_at->toAtomString().'</lastmod>', escape: false);
    }

    public function test_the_sitemap_is_not_found_for_an_unpublished_resume(): void
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);
        Resume::factory()->for($owner)->create();

        $this->get('/sitemap.xml')->assertNotFound();
    }

    public function test_the_sitemap_is_not_found_for_another_users_resume(): void
    {
        User::factory()->create(['email' => self::ADMIN_EMAIL]);
        Resume::factory()->published()->create();

        $this->get('/sitemap.xml')->assertNotFound();
    }

    public function test_the_sitemap_fails_when_the_admin_email_is_not_set(): void
    {
        \Config::set('admin.email', null);

        $this->withoutExceptionHandling();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Admin email is not set');

        $this->get('/sitemap.xml');
    }

    public function test_robots_allows_all_crawlers_and_points_to_the_sitemap(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $this->assertSame(
            "User-agent: *\nDisallow:\n\nSitemap: ".route('sitemap')."\n",
            $response->getContent()
        );
    }

    public function test_robots_works_without_an_admin_email(): void
    {
        \Config::set('admin.email', null);

        $this->get('/robots.txt')->assertOk();
    }
}
