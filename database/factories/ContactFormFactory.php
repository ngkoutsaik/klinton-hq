<?php

namespace Database\Factories;

use App\Models\ContactForm;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ContactForm>
 */
class ContactFormFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(),
            'message' => $this->faker->paragraph(),
            'send_copy' => $this->faker->boolean(),
        ];
    }

    /**
     * Indicate that the email has been sent.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'sent' => true,
        ]);
    }
}
