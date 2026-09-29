<?php

namespace Database\Factories;

use App\Models\Resume;
use App\Models\WorkExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WorkExperience>
 */
class WorkExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'role_name' => $this->faker->name(),
            'company_name' => $this->faker->name(),
            'location' => $this->faker->word(),
            'description' => $this->faker->text(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'in_progress' => $this->faker->boolean(),
            'resume_id' => Resume::factory()->create()->get('id'),
        ];
    }
}
