<?php

namespace Database\Factories;

use App\Enums\ResumeEntryType;
use App\Models\Resume;
use App\Models\ResumeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeEntry>
 */
class ResumeEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->name(),
            'organization' => $this->faker->name(),
            'location' => $this->faker->word(),
            'description' => $this->faker->text(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'in_progress' => $this->faker->boolean(),
            'resume_id' => Resume::factory(),
            'type' => ResumeEntryType::WORK,
        ];
    }

    public function education(): ResumeEntryFactory|Factory
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => ResumeEntryType::EDUCATION,
            ];
        });
    }
}
