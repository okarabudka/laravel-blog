<?php

use App\Models\Post;
use function Pest\Laravel\get;

test('previous and next link to the neighbouring posts', function () {
    $oldest = Post::factory()->create(['title' => 'Oldest post', 'published_at' => now()->subDays(4)]);
    $previous = Post::factory()->create(['title' => 'Previous post', 'published_at' => now()->subDays(3)]);
    $current = Post::factory()->create(['title' => 'Current post', 'published_at' => now()->subDays(2)]);
    $next = Post::factory()->create(['title' => 'Next post', 'published_at' => now()->subDays(1)]);
    $newest = Post::factory()->create(['title' => 'Newest post', 'published_at' => now()->subHour()]);

    $response = get(route('view', $current))->assertOk();

    expect(viewVariable($response, 'prev', Post::class)->is($previous))->toBeTrue()
        ->and(viewVariable($response, 'next', Post::class)->is($next))->toBeTrue();
});

test('posts published on the same day are still neighbours', function () {
    $morning = Post::factory()->create(['published_at' => today()->subDay()->setTime(9, 0)]);
    $evening = Post::factory()->create(['published_at' => today()->subDay()->setTime(18, 0)]);

    expect(viewVariable(get(route('view', $evening)), 'prev', Post::class)->is($morning))->toBeTrue()
        ->and(viewVariable(get(route('view', $morning)), 'next', Post::class)->is($evening))->toBeTrue();
});

test('the first and the last post have no previous or next link', function () {
    $first = Post::factory()->create(['published_at' => now()->subDays(2)]);
    $last = Post::factory()->create(['published_at' => now()->subDay()]);
    Post::factory()->draft()->create(['published_at' => now()->subDays(3)]);
    Post::factory()->scheduled()->create();

    expect(get(route('view', $first))->viewData('prev'))->toBeNull()
        ->and(get(route('view', $last))->viewData('next'))->toBeNull();
});
