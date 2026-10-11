<?php

namespace Tests\Feature;

use App\Enums\ResumeEntryType;
use App\Models\Resume;
use App\Models\ResumeEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResumeEntryTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_relation_only_returns_entries_of_its_own_type(): void
    {
        $resume = Resume::factory()->create();
        $work = ResumeEntry::factory()->recycle($resume)->create();
        $education = ResumeEntry::factory()->recycle($resume)->education()->create();

        $this->assertTrue($resume->workExperiences->sole()->is($work));
        $this->assertTrue($resume->education->sole()->is($education));
        $this->assertCount(2, $resume->resumeEntries);
    }

    public function test_creating_through_the_education_relation_stores_an_education_entry(): void
    {
        $resume = Resume::factory()->create();

        $entry = $resume->education()->create([
            'title' => 'BSc Computer Science',
            'organization' => 'University of Ljubljana',
            'start_year' => 2010,
        ]);

        $this->assertSame(ResumeEntryType::EDUCATION, $entry->fresh()->type);
    }

    public function test_creating_through_the_work_relation_stores_a_work_entry(): void
    {
        $resume = Resume::factory()->create();

        $entry = $resume->workExperiences()->create([
            'title' => 'Developer',
            'organization' => 'Acme',
            'start_year' => 2020,
            'description' => '<p>Built things.</p>',
        ]);

        $this->assertSame(ResumeEntryType::WORK, $entry->fresh()->type);
    }

    public function test_saving_an_ongoing_entry_clears_its_end_date(): void
    {
        $entry = ResumeEntry::factory()->create(['in_progress' => false, 'end_year' => 2020, 'end_month' => 5]);

        $entry->update(['in_progress' => true]);

        $this->assertNull($entry->fresh()->end_year);
        $this->assertNull($entry->fresh()->end_month);
    }
}
