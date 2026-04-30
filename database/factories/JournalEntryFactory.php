<?php

namespace Database\Factories;

use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JournalEntry>
 */
class JournalEntryFactory extends Factory
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
            'entry_type' => JournalEntry::TYPE_THREE_GOOD_THINGS,
            'what_went_well' => fake()->sentence(6),
            'my_contribution' => fake()->sentence(6),
            'content' => [
                'what_went_well' => fake()->sentence(6),
                'my_contribution' => fake()->sentence(6),
            ],
        ];
    }

    public function strengthsReflection(): static
    {
        return $this->state(fn (): array => [
            'entry_type' => JournalEntry::TYPE_STRENGTHS_REFLECTION,
            'what_went_well' => '',
            'my_contribution' => '',
            'content' => [
                'strength_key' => fake()->randomElement(array_keys(JournalEntry::strengthOptions())),
                'situation' => fake()->sentence(6),
                'how_used' => fake()->sentence(7),
                'reflection' => fake()->paragraph(),
            ],
        ]);
    }
}
