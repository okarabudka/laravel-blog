<?php

namespace Database\Factories;

use App\Models\TextWidget;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TextWidget>
 */
class TextWidgetFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->slug(2),
            'image' => null,
            'title' => fake()->sentence(3),
            'content' => '<p>'.fake()->paragraph().'</p>',
            'active' => true,
        ];
    }
}
