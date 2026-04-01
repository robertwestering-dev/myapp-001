<?php

namespace Database\Factories;

use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'author_id' => User::factory()->admin(),
            'slug' => $this->faker->unique()->slug(4),
            'cover_image_url' => $this->faker->imageUrl(1600, 900, 'business', true),
            'tags' => $this->faker->randomElements([
                'Adaptability',
                'Digitale transformatie',
                'Leiderschap',
                'AI adoptie',
                'Digitale weerbaarheid',
                'Teamontwikkeling',
            ], $this->faker->numberBetween(2, 4)),
            'title' => $this->translatedValues(fn (): string => Str::headline($this->faker->words(4, true))),
            'excerpt' => $this->translatedValues(fn (): string => $this->faker->sentence(18)),
            'content' => $this->translatedValues(fn (): string => implode("\n\n", [
                '# '.Str::headline($this->faker->words(5, true)),
                $this->faker->paragraphs(2, true),
                '## '.Str::headline($this->faker->words(3, true)),
                $this->faker->paragraphs(3, true),
                '- '.implode("\n- ", $this->faker->sentences(3)),
            ])),
            'is_published' => true,
            'is_featured' => false,
            'published_at' => now()->subDays($this->faker->numberBetween(1, 30)),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'is_published' => false,
            'is_featured' => false,
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'is_featured' => true,
        ]);
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
