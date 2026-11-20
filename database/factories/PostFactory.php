<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = rtrim(fake()->unique()->sentence(6), '.');

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'thumbnail' => null,
            'body' => collect(range(1, 6))->map(fn () => '<p>'.fake()->paragraph().'</p>')->implode(''),
            'active' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
            'user_id' => User::factory(),
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => [
            'active' => true,
            'published_at' => fake()->dateTimeBetween('-1 year', '-1 day'),
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'active' => false,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => [
            'active' => true,
            'published_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
        ]);
    }
}
