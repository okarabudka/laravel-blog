<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\UpvoteDownvote;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UpvoteDownvote>
 */
class UpvoteDownvoteFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'is_upvote' => true,
            'post_id' => Post::factory(),
            'user_id' => User::factory(),
        ];
    }

    public function downvote(): static
    {
        return $this->state(fn () => ['is_upvote' => false]);
    }
}
