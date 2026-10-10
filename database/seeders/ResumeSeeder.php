<?php

namespace Database\Seeders;

use App\Enums\LinkIcon;
use App\Models\Resume;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Seeder;

class ResumeSeeder extends Seeder
{
    /**
     * Seed a published resume for the given owner with fixed content, so the page and PDF look the same on every seed.
     */
    public function run(User $owner): void
    {
        $resume = Resume::factory()->published()->for($owner)->create([
            'title' => 'Senior Backend Developer',
            'intro' => '<p>Backend developer with 8+ years building <strong>Laravel</strong> and <strong>Drupal</strong> applications. I care about clean data models, fast test suites and shipping small, safe changes.</p>',
            'looking_for_role' => true,
        ]);

        $resume->workExperiences()->createMany([
            [
                'title' => 'Senior Backend Developer',
                'organization' => 'Acme Digital',
                'location' => 'Athens, Greece',
                'description' => '<ul><li>Led the migration of a legacy monolith to Laravel</li><li>Cut CI time from 25 to 6 minutes by parallelising the test suite</li><li>Introduced queued PDF generation for invoices and reports</li></ul>',
                'start_date' => '2022-03-01',
                'end_date' => null,
                'in_progress' => true,
                'order' => 0,
            ],
            [
                'title' => 'Backend Developer',
                'organization' => 'Northwind Agency',
                'location' => 'Remote',
                'description' => '<ul><li>Built and maintained Drupal 9/10 sites for public sector clients</li><li>Introduced automated deployments with GitLab CI</li></ul>',
                'start_date' => '2019-01-01',
                'end_date' => '2022-02-28',
                'in_progress' => false,
                'order' => 1,
            ],
            [
                'title' => 'Junior PHP Developer',
                'organization' => 'Startly',
                'location' => 'Thessaloniki, Greece',
                'description' => '<p>Feature work on a SaaS invoicing platform built with Laravel and Vue.</p>',
                'start_date' => '2017-06-01',
                'end_date' => '2018-12-31',
                'in_progress' => false,
                'order' => 2,
            ],
        ]);

        $resume->education()->createMany([
            [
                'title' => 'MSc Software Engineering',
                'organization' => 'University of Edinburgh',
                'location' => 'Edinburgh, UK',
                'description' => '<p>Thesis on event-sourced architectures.</p>',
                'start_date' => '2015-09-01',
                'end_date' => '2016-09-30',
                'in_progress' => false,
                'order' => 0,
            ],
            [
                'title' => 'BSc Computer Science',
                'organization' => 'Aristotle University of Thessaloniki',
                'location' => 'Thessaloniki, Greece',
                'description' => null,
                'start_date' => '2011-09-01',
                'end_date' => '2015-06-30',
                'in_progress' => false,
                'order' => 1,
            ],
        ]);

        $resume->links()->createMany([
            ['title' => 'GitHub', 'url' => 'https://github.com/example', 'icon' => LinkIcon::GITHUB, 'open_in_new_tab' => true],
            ['title' => 'LinkedIn', 'url' => 'https://www.linkedin.com/in/example', 'icon' => LinkIcon::LINKEDIN, 'open_in_new_tab' => true],
            ['title' => 'Drupal.org', 'url' => 'https://www.drupal.org/u/example', 'icon' => LinkIcon::DRUPAL, 'open_in_new_tab' => true],
        ]);

        $resume->extraInfo()->createMany([
            ['title' => 'Location', 'value' => 'Athens, Greece', 'is_active' => true, 'order' => 0],
            ['title' => 'Availability', 'value' => 'Open to remote', 'is_active' => true, 'order' => 1],
            ['title' => 'Languages', 'value' => 'Greek, English', 'is_active' => false, 'order' => 2],
        ]);

        $skillIds = array_map(
            fn (string $name): int => Skill::firstOrCreate(['name' => $name])->id,
            ['PHP', 'Laravel', 'Drupal', 'MySQL', 'Docker', 'Vue.js', 'GitLab CI', 'PHPUnit'],
        );

        $resume->skills()->attach($skillIds, ['is_active' => true]);
    }
}
