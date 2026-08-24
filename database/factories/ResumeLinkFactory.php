<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\Resume;
use App\Models\ResumeLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ResumeLink>
 */
class ResumeLinkFactory extends Factory
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
            'link_id' => Link::factory()->create()->get('id'),
        ];
    }
}
