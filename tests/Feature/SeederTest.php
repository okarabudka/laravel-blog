<?php

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use App\Models\TextWidget;
use App\Models\UpvoteDownvote;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Storage;

beforeEach(fn () => Storage::fake('public'));

test('the demo seeder fills the blog and can run twice', function () {
    $this->seed(DatabaseSeeder::class);
    $this->seed(DatabaseSeeder::class);

    expect(Post::published()->count())->toBe(25)
        ->and(Post::where('active', false)->count())->toBe(3)
        ->and(Post::where('active', true)->where('published_at', '>', now())->count())->toBe(2)
        ->and(Category::count())->toBe(6)
        ->and(User::count())->toBe(6)
        ->and(User::where('email', 'admin@example.com')->sole()->isAdmin())->toBeTrue()
        ->and(TextWidget::pluck('key')->sort()->values()->all())->toBe(['about-us', 'about-us-sidebar', 'header'])
        ->and(UpvoteDownvote::count())->toBeGreaterThan(0)
        ->and(PostView::count())->toBeGreaterThan(0)
        ->and(Comment::count())->toBeGreaterThan(0);

    Post::all()->each(fn (Post $post) => expect($post->categories)->not->toBeEmpty());
    Storage::disk('public')->assertExists(Post::firstOrFail()->thumbnail);
});

test('the seeded blog renders its pages', function () {
    $this->seed(DatabaseSeeder::class);

    $this->get(route('home'))->assertOk()->assertSee('Laravel Blog');
    $this->get(route('about'))->assertOk()->assertSee('About this blog');
    $this->get(route('by-category', Category::firstOrFail()))->assertOk();
    $this->get(route('view', Post::published()->firstOrFail()))->assertOk();
});

test('demo readers can sign in with the documented password', function () {
    $this->seed(DatabaseSeeder::class);

    $this->post('/login', ['email' => 'reader1@example.com', 'password' => 'password']);

    $this->assertAuthenticated();
});
