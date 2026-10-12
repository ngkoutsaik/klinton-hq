<?php

namespace Tests\Feature;

use App\Enums\SkillType;
use App\Models\Resume;
use App\Models\ResumeEntry;
use App\Models\Skill;
use App\Models\User;
use Exception;
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

    public function test_it_shows_work_experience_and_education_in_their_own_sections(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->create([
            'title' => 'Backend Developer',
            'organization' => 'Acme',
        ]);
        ResumeEntry::factory()->recycle($resume)->education()->create([
            'title' => 'BSc Computer Science',
            'organization' => 'University of Ljubljana',
        ]);

        $this->get('/')->assertOk()->assertSeeTextInOrder([
            'Work Experience',
            'Acme – Backend Developer',
            'Education',
            'University of Ljubljana – BSc Computer Science',
        ]);
    }

    public function test_it_hides_the_education_section_when_there_is_no_education(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->create();

        $this->get('/')->assertOk()->assertDontSee('>Education</h2>', escape: false);
    }

    public function test_it_groups_active_skills_by_type_in_their_own_sections(): void
    {
        $resume = $this->createOwnersResume();
        $resume->skills()->attach(Skill::factory()->create(['name' => 'Greek', 'type' => SkillType::SPOKEN_LANGUAGE]), ['order' => 0]);
        $resume->skills()->attach(Skill::factory()->create(['name' => 'Laravel', 'type' => SkillType::LANGUAGE_FRAMEWORK]), ['order' => 2]);
        $resume->skills()->attach(Skill::factory()->create(['name' => 'PHP', 'type' => SkillType::LANGUAGE_FRAMEWORK]), ['order' => 1]);
        $resume->skills()->attach(Skill::factory()->create(['name' => 'Cobol', 'type' => SkillType::LANGUAGE_FRAMEWORK]), ['is_active' => false]);

        $this->get('/')->assertOk()
            ->assertSeeTextInOrder(['Skills', 'Languages & Frameworks', 'PHP', 'Laravel', 'Spoken Languages', 'Greek'])
            ->assertDontSeeText('Cobol');
    }

    public function test_it_hides_a_skill_section_when_it_has_no_active_skills(): void
    {
        $resume = $this->createOwnersResume();
        $resume->skills()->attach(Skill::factory()->create(['type' => SkillType::LANGUAGE_FRAMEWORK]));

        $this->get('/')->assertOk()->assertDontSeeText('Spoken Languages');
    }

    public function test_it_shows_an_education_entry_without_a_description(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->education()->create([
            'organization' => 'University of Ljubljana',
            'description' => null,
        ]);

        $this->get('/')->assertOk()->assertSeeText('University of Ljubljana');
    }

    public function test_it_uses_the_current_job_and_not_ongoing_education_as_the_employer(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->create([
            'organization' => 'Acme',
            'end_year' => null,
            'in_progress' => true,
        ]);
        ResumeEntry::factory()->recycle($resume)->education()->create([
            'organization' => 'University of Ljubljana',
            'end_year' => null,
            'in_progress' => true,
            'order' => 0,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('"worksFor":{"@type":"Organization","name":"Acme"}', escape: false);
    }

    public function test_it_does_not_list_an_employer_when_only_education_is_ongoing(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->education()->create([
            'end_year' => null,
            'in_progress' => true,
        ]);

        $this->get('/')->assertOk()->assertDontSee('"worksFor"', escape: false);
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

    public function test_it_fails_when_the_admin_email_is_empty(): void
    {
        config(['admin.email' => '']);

        $this->withoutExceptionHandling();
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Admin email is not set');

        $this->get('/');
    }

    private function createOwnersResume(): Resume
    {
        $owner = User::factory()->create(['email' => self::ADMIN_EMAIL]);

        return Resume::factory()->published()->for($owner)->create();
    }

    public function test_it_shows_the_month_only_for_dates_that_have_one(): void
    {
        $resume = $this->createOwnersResume();
        ResumeEntry::factory()->recycle($resume)->create([
            'start_year' => 2015,
            'start_month' => null,
            'end_year' => 2018,
            'end_month' => 3,
            'in_progress' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('<time datetime="2015">2015</time>', escape: false)
            ->assertSee('<time datetime="2018-03">03/2018</time>', escape: false);
    }
}
