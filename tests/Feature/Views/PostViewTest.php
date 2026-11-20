<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\withHeader;

test('a guest view is recorded without a user', function () {
    $post = Post::factory()->create();

    withHeader('User-Agent', 'PestBrowser/1.0')->get(route('view', $post))->assertOk();

    expect(PostView::sole()->only(['post_id', 'user_id', 'user_agent']))
        ->toBe(['post_id' => $post->id, 'user_id' => null, 'user_agent' => 'PestBrowser/1.0']);
});

test('a reader view is recorded with the reader', function () {
    $reader = User::factory()->create();
    $post = Post::factory()->create();

    actingAs($reader)->get(route('view', $post))->assertOk();

    expect(PostView::sole()->user_id)->toBe($reader->id);
});

test('every visit counts', function () {
    $post = Post::factory()->create();

    get(route('view', $post));
    get(route('view', $post));

    expect(PostView::where('post_id', $post->id)->count())->toBe(2);
});

test('drafts and scheduled posts record no view', function (string $state) {
    $post = postInState($state);

    get(route('view', $post))->assertNotFound();

    expect(PostView::count())->toBe(0);
})->with(['draft', 'scheduled']);

test('a view is recorded for clients without or with a very long user agent', function (?string $agent) {
    $post = Post::factory()->create();

    withHeader('User-Agent', (string) $agent)->get(route('view', $post))->assertOk();

    expect(mb_strlen(PostView::sole()->user_agent))->toBeLessThanOrEqual(255);
})->with([
    'none' => [''],
    'long' => [str_repeat('Mozilla/5.0 ', 40)],
]);
