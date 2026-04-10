<?php

namespace Database\Factories;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ForumThread>
 */
class ForumThreadFactory extends Factory
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
            'slug' => $this->faker->unique()->slug(5),
            'discussion_type' => $this->faker->randomElement(ForumThread::discussionTypeOptions()),
            'title' => Str::headline($this->faker->words(5, true)),
            'body' => implode("\n\n", [
                '# '.Str::headline($this->faker->words(4, true)),
                $this->faker->paragraphs(2, true),
                '- '.implode("\n- ", $this->faker->sentences(3)),
            ]),
            'tags' => $this->faker->randomElements([
                'Digitale transformatie',
                'AI adoptie',
                'Leiderschap',
                'Digitale weerbaarheid',
                'Samenwerking',
            ], $this->faker->numberBetween(1, 3)),
            'is_locked' => false,
            'last_activity_at' => now()->subHours($this->faker->numberBetween(1, 48)),
        ];
    }

    public function question(): static
    {
        return $this->state(fn (): array => [
            'discussion_type' => ForumThread::TYPE_QUESTION,
        ]);
    }
}
