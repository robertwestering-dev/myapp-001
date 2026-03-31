<?php

namespace Database\Factories;

use App\Models\AcademyCourse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AcademyCourse>
 */
class AcademyCourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(),
            'theme' => fake()->randomElement(array_keys(AcademyCourse::themes())),
            'path' => 'academy-courses/'.fake()->unique()->slug(),
            'estimated_minutes' => fake()->numberBetween(20, 60),
            'sort_order' => fake()->numberBetween(1, 100),
            'is_active' => true,
            'title' => $this->translatedValues(fn (): string => fake()->sentence(3)),
            'audience' => $this->translatedValues(fn (): string => fake()->sentence(8)),
            'goal' => $this->translatedValues(fn (): string => fake()->sentence(10)),
            'summary' => $this->translatedValues(fn (): string => fake()->paragraph()),
            'learning_goals' => $this->translatedValues(fn (): array => fake()->sentences(3)),
            'contents' => $this->translatedValues(fn (): array => fake()->sentences(3)),
        ];
    }

    /**
     * @param  callable(): mixed  $resolver
     * @return array<string, mixed>
     */
    protected function translatedValues(callable $resolver): array
    {
        return collect(array_keys(config('locales.supported', [])))
            ->mapWithKeys(fn (string $locale): array => [$locale => $resolver()])
            ->all();
    }
}
