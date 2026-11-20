<?php

namespace Database\Seeders;

use App\Models\TextWidget;
use Illuminate\Database\Seeder;

class TextWidgetSeeder extends Seeder
{
    public function run(): void
    {
        $widgets = [
            'header' => [
                'title' => 'Laravel Blog',
                'content' => 'Notes on building and running web applications with Laravel.',
            ],
            'about-us' => [
                'title' => 'About this blog',
                'content' => '<p>This blog collects practical notes from day-to-day work on PHP and Laravel '
                    .'applications: upgrades, testing, deployment and the small decisions that keep a code base '
                    .'healthy.</p><p>Posts are written in the admin panel, readers can vote and comment once '
                    .'they have verified their email address.</p>',
                'image' => PostSeeder::copyCover(7),
            ],
            'about-us-sidebar' => [
                'title' => 'About',
                'content' => 'Practical notes on Laravel, PHP, testing and running applications in production.',
            ],
        ];

        foreach ($widgets as $key => $attributes) {
            TextWidget::firstOrCreate(['key' => $key], $attributes + ['active' => true]);
        }
    }
}
