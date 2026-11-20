<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PostView>
 */
class PostViewFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'post_id' => Post::factory(),
            'user_id' => null,
        ];
    }
}
