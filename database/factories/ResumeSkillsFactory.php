<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\ResumeSkills;
use App\Models\Skills;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeSkills>
 */
class ResumeSkillsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'resume_id' => Resume::factory()->create()->get('id'),
            'skill_id'=>Skills::factory()->create()->get('id'),
        ];
    }
}
