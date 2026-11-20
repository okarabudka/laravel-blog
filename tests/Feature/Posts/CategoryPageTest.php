<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Pagination\LengthAwarePaginator;
use function Pest\Laravel\get;

test('the category page lists its published posts with pagination', function () {
    $category = Category::factory()->create(['title' => 'Travel']);
    $other = Category::factory()->create();

    $posts = Post::factory()->count(12)->hasAttached($category)->create();
    $foreign = Post::factory()->count(3)->hasAttached($other)->create();
    $draft = Post::factory()->draft()->hasAttached($category)->create();

    $response = get(route('by-category', $category))->assertOk()->assertSee('Travel');

    $listed = viewVariable($response, 'posts', LengthAwarePaginator::class);
    expect($listed->total())->toBe(12)
        ->and($listed->count())->toBe(10)
        ->and($listed->getCollection()->pluck('id')->diff($posts->pluck('id')))->toBeEmpty();

    $response->assertSee(route('by-category', ['category' => $category, 'page' => 2]), false)
        ->assertDontSee(route('view', $draft), false);

    foreach ($foreign as $post) {
        $response->assertDontSee(route('view', $post), false);
    }
});

test('post links on the category page use the slug of each post', function () {
    $category = Category::factory()->create();
    Post::factory()->hasAttached($category)->create(['slug' => 'a-post-about-trains']);

    get(route('by-category', $category))
        ->assertOk()
        ->assertSee(route('view', 'a-post-about-trains'), false);
});

test('the second page lists the remaining posts', function () {
    $category = Category::factory()->create();
    Post::factory()->count(12)->hasAttached($category)->create();

    $page = get(route('by-category', ['category' => $category, 'page' => 2]));

    expect(viewVariable($page, 'posts', LengthAwarePaginator::class)->count())->toBe(2);
});
