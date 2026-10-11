<?php

namespace Tests\Feature;

use App\Enums\ResumeEntryType;
use App\Enums\SkillType;
use App\Filament\Resources\Resumes\Pages\CreateResume;
use App\Filament\Resources\Resumes\Pages\EditResume;
use App\Models\Resume;
use App\Models\ResumeEntry;
use App\Models\Skill;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ResumeFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidLinkUrls(): array
    {
        return [
            'javascript' => ['javascript:alert(1)'],
            'data' => ['data:text/html,<script>alert(1)</script>'],
            'ftp' => ['ftp://example.com'],
            'not a url' => ['not a url'],
        ];
    }

    #[DataProvider('invalidLinkUrls')]
    public function test_it_rejects_link_urls_that_are_not_http_or_https(string $url): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateResume::class)
            ->fillForm(['links' => [['title' => 'Bad link', 'url' => $url]]])
            ->call('create')
            ->assertHasFormErrors(['links.0.url']);

        $undoRepeaterFake();
    }

    public function test_it_accepts_https_link_urls(): void
    {
        $undoRepeaterFake = Repeater::fake();

        Livewire::test(CreateResume::class)
            ->fillForm(['links' => [['title' => 'GitHub', 'url' => 'https://github.com/example']]])
            ->call('create')
            ->assertHasNoFormErrors(['links.0.url']);

        $undoRepeaterFake();
    }

    public function test_it_saves_each_repeater_with_its_own_entry_type(): void
    {
        $component = Livewire::test(CreateResume::class);
        $state = $component->get('data');
        $workKey = array_key_first($state['workExperiences']);
        $educationKey = array_key_first($state['education']);

        $component
            ->set('data.title', 'Resume')
            ->set('data.intro', '<p>Intro</p>')
            ->set('data.user.first_name', 'Jane')
            ->set('data.user.last_name', 'Doe')
            ->set('data.extraInfo', [])
            ->set('data.links', [])
            ->set('data.resumeSkills', [])
            ->set("data.workExperiences.$workKey", [
                'title' => 'Backend Developer',
                'organization' => 'Acme',
                'location' => 'Ljubljana',
                'start_year' => 2020,
                'end_year' => 2022,
                'description' => '<p>Built things.</p>',
            ])
            ->set("data.education.$educationKey", [
                'title' => 'BSc Computer Science',
                'organization' => 'University of Ljubljana',
                'location' => 'Ljubljana',
                'start_year' => 2010,
                'end_year' => 2014,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('resume_entries', ['title' => 'Backend Developer', 'type' => ResumeEntryType::WORK]);
        $this->assertDatabaseHas('resume_entries', ['title' => 'BSc Computer Science', 'type' => ResumeEntryType::EDUCATION]);
    }

    public function test_it_attaches_skills_through_the_repeater(): void
    {
        $undoRepeaterFake = Repeater::fake();
        $resume = Resume::factory()->create();
        $skill = Skill::factory()->create();

        Livewire::test(EditResume::class, ['record' => $resume->getRouteKey()])
            ->fillForm(['resumeSkills' => [['skill_id' => $skill->getKey(), 'is_active' => false]]])
            ->call('save')
            ->assertHasNoFormErrors(['resumeSkills']);

        $this->assertDatabaseHas('resume_skills', ['resume_id' => $resume->getKey(), 'skill_id' => $skill->getKey(), 'is_active' => false]);

        $undoRepeaterFake();
    }

    public function test_it_shows_each_skills_type_in_the_repeater(): void
    {
        $resume = Resume::factory()->create();
        $resume->skills()->attach(Skill::factory()->create(['type' => SkillType::TOOL]));

        Livewire::test(EditResume::class, ['record' => $resume->getRouteKey()])
            ->assertSeeText(SkillType::TOOL->getLabel());
    }

    public function test_it_loads_each_entry_into_the_repeater_for_its_type(): void
    {
        $resume = Resume::factory()->create();
        ResumeEntry::factory()->recycle($resume)->create(['title' => 'Backend Developer']);
        ResumeEntry::factory()->recycle($resume)->education()->create(['title' => 'BSc Computer Science']);

        $state = Livewire::test(EditResume::class, ['record' => $resume->getRouteKey()])->get('data');

        $this->assertSame(['Backend Developer'], array_column($state['workExperiences'], 'title'));
        $this->assertSame(['BSc Computer Science'], array_column($state['education'], 'title'));
    }

    public function test_it_rejects_an_end_year_before_the_start_year(): void
    {
        [$component, $key] = $this->createWithWorkEntry(['start_year' => 2020, 'end_year' => 2019]);

        $component->assertHasFormErrors(["workExperiences.$key.end_year" => 'gte']);
    }

    public function test_it_requires_an_end_year_unless_the_entry_is_in_progress(): void
    {
        [$component, $key] = $this->createWithWorkEntry(['start_year' => 2020, 'in_progress' => false]);
        $component->assertHasFormErrors(["workExperiences.$key.end_year" => 'required']);

        [$component, $key] = $this->createWithWorkEntry(['start_year' => 2020, 'in_progress' => true]);
        $component->assertHasNoFormErrors(["workExperiences.$key.end_year"]);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array{Testable, string}
     */
    private function createWithWorkEntry(array $entry): array
    {
        $component = Livewire::test(CreateResume::class);
        $key = array_key_first($component->get('data.workExperiences'));

        $component
            ->set("data.workExperiences.$key", array_merge($component->get("data.workExperiences.$key"), $entry))
            ->call('create');

        return [$component, $key];
    }
}
