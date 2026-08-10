<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use App\Models\UserLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserLink>
 */
class UserLinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create()->get('id'),
            'link_id' => Link::factory()->create()->get('id'),
        ];
    }
}
