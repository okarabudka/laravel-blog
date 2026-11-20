<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public const int PUBLISHED = 25;

    public const int DRAFTS = 3;

    public const int SCHEDULED = 2;

    private const int COVERS = 8;

    /**
     * @var list<array{string, string}> title and main category
     */
    private const array POSTS = [
        ['Upgrading a Laravel 10 app to Laravel 13 in one weekend', 'Laravel'],
        ['Why the slim application skeleton is worth the move', 'Laravel'],
        ['Policies instead of if statements in controllers', 'Laravel'],
        ['Livewire components that stay small', 'Laravel'],
        ['Queues, retries and the jobs that never finish', 'Laravel'],
        ['Readonly classes in everyday PHP', 'PHP'],
        ['Enums that carry behaviour', 'PHP'],
        ['What PHP 8.5 changes for application code', 'PHP'],
        ['Static analysis at the strictest level without tears', 'PHP'],
        ['Composer constraints that do not bite later', 'PHP'],
        ['Regression tests that fail before the fix', 'Testing'],
        ['Pest datasets for permission tables', 'Testing'],
        ['Faking time instead of sleeping in tests', 'Testing'],
        ['Testing Livewire components without a browser', 'Testing'],
        ['When SQLite tests lie about MariaDB', 'Testing'],
        ['A Docker setup that new colleagues can start in minutes', 'DevOps'],
        ['Health checks that mean something', 'DevOps'],
        ['Secrets scanning in every pull request', 'DevOps'],
        ['Zero-downtime migrations for busy tables', 'DevOps'],
        ['Logs you can actually search', 'DevOps'],
        ['A stylesheet without a build step', 'Frontend'],
        ['Alpine.js for the small things', 'Frontend'],
        ['Accessible buttons are just buttons', 'Frontend'],
        ['Pagination markup that styles itself', 'Frontend'],
        ['Reading other people\'s code for an hour a day', 'Career'],
        ['Writing pull request descriptions that get reviewed', 'Career'],
        ['How to leave a legacy project better than you found it', 'Career'],
        ['Draft: notes on database indexes', 'PHP'],
        ['Draft: a checklist for code reviews', 'Career'],
        ['Coming soon: search with full-text indexes', 'Laravel'],
    ];

    public function run(): void
    {
        if (Post::exists()) {
            return;
        }

        fake()->seed(2026);

        $author = User::where('email', UserSeeder::ADMIN_EMAIL)->firstOrFail();
        $categories = Category::pluck('id', 'title');

        foreach (self::POSTS as $index => [$title, $category]) {
            $extra = fake()->randomElements($categories->except($category)->values()->all(), fake()->numberBetween(0, 2));

            $post = Post::create([
                'title' => $title,
                'slug' => Str::slug($title),
                'thumbnail' => self::copyCover($index % self::COVERS + 1),
                'body' => $this->body(),
                'active' => $index < self::PUBLISHED + self::DRAFTS ? $index < self::PUBLISHED : true,
                'published_at' => $this->publishedAt($index),
                'user_id' => $author->id,
                'meta_title' => $title.' | Laravel Blog',
                'meta_description' => Str::limit(fake()->realText(200), 155),
            ]);

            $post->categories()->sync([$categories[$category], ...$extra]);
        }
    }

    /**
     * Copies a bundled cover to the public disk and returns its path there.
     */
    public static function copyCover(int $number): string
    {
        $path = "posts/cover-{$number}.svg";
        Storage::disk('public')->put($path, (string) file_get_contents(__DIR__."/images/cover-{$number}.svg"));

        return $path;
    }

    private function publishedAt(int $index): \DateTimeInterface
    {
        if ($index >= self::PUBLISHED + self::DRAFTS) {
            return now()->addDays(($index - self::PUBLISHED - self::DRAFTS + 1) * 5)->setTime(9, 0);
        }

        // Newest post first in the list, a few days apart.
        return now()->subDays($index * 4 + 1)->setTime(fake()->numberBetween(7, 20), 15 * fake()->numberBetween(0, 3));
    }

    private function body(): string
    {
        $paragraphs = array_map(
            fn () => '<p>'.e(fake()->realText(fake()->numberBetween(350, 700))).'</p>',
            range(1, fake()->numberBetween(4, 7)),
        );

        array_splice($paragraphs, 2, 0, '<h2>'.e(rtrim(fake()->realText(40), '.')).'</h2>');

        return implode("\n", $paragraphs);
    }
}
