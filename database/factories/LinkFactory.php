<?php

namespace Database\Factories;

use App\Enums\LinkIcon;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
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
            'target' => $this->faker->words(),
            'icon' => $this->faker->randomElement(LinkIcon::class),
            'open_in_new_tab' => $this->faker->boolean(),
        ];
    }
}
