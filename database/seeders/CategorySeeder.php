<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public const array TITLES = ['Laravel', 'PHP', 'Testing', 'DevOps', 'Frontend', 'Career'];

    public function run(): void
    {
        foreach (self::TITLES as $title) {
            Category::firstOrCreate(['slug' => Str::slug($title)], ['title' => $title]);
        }
    }
}
