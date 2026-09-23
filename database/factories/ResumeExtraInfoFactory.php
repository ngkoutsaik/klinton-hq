<?php

namespace Database\Factories;

use App\Models\ResumeExtraInfo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeExtraInfo>
 */
class ResumeExtraInfoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'value' => $this->faker->word(),
        ];
    }
}
