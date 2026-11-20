<?php

use App\Models\Post;

use function Pest\Laravel\get;

test('a post published an hour ago is the latest post on the home page', function () {
    Post::factory()->create(['title' => 'Older post', 'published_at' => now()->subWeek()]);
    Post::factory()->create(['title' => 'Fresh post', 'published_at' => now()->subHour()]);

    get(route('home'))
        ->assertOk()
        ->assertSeeInOrder(['Latest post', 'Fresh post', 'Popular posts']);
});

test('scheduled posts and drafts are not published', function () {
    $published = Post::factory()->create(['published_at' => now()->subMinute()]);
    Post::factory()->scheduled()->create();
    Post::factory()->draft()->create();
    Post::factory()->create(['published_at' => null]);

    expect(Post::published()->pluck('id')->all())->toBe([$published->id]);
});

test('a scheduled post page is not found', function () {
    $post = Post::factory()->scheduled()->create();

    get(route('view', $post))->assertNotFound();
});
