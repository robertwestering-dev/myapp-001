<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'naam' => fake()->unique()->company(),
            'adres' => fake()->streetAddress(),
            'postcode' => strtoupper(fake()->bothify('#### ??')),
            'plaats' => fake()->city(),
            'land' => fake()->country(),
            'telefoon' => fake()->phoneNumber(),
            'contact_id' => User::factory(),
        ];
    }
}
