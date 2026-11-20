<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Demo blog for local development. Safe to run repeatedly: existing data is kept.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TextWidgetSeeder::class,
            PostSeeder::class,
            EngagementSeeder::class,
        ]);
    }
}
