<?php

namespace Database\Factories;

use App\Models\ThreeGoodThingsEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ThreeGoodThingsEntry>
 */
class ThreeGoodThingsEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'entry_date' => fake()->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
            'what_went_well' => fake()->sentence(6),
            'my_contribution' => fake()->sentence(6),
        ];
    }
}
