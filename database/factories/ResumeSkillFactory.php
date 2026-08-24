<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\ResumeSkill;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeSkill>
 */
class ResumeSkillFactory extends Factory
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
            'skill_id' => Skill::factory()->create()->get('id'),
        ];
    }
}
