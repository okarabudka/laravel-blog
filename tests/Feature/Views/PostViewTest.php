<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;

test('a guest view is recorded without a user', function () {
    $post = Post::factory()->create();

    $this->withHeader('User-Agent', 'PestBrowser/1.0')->get(route('view', $post))->assertOk();

    expect(PostView::sole()->only(['post_id', 'user_id', 'user_agent']))
        ->toBe(['post_id' => $post->id, 'user_id' => null, 'user_agent' => 'PestBrowser/1.0']);
});

test('a reader view is recorded with the reader', function () {
    $reader = User::factory()->create();
    $post = Post::factory()->create();

    $this->actingAs($reader)->get(route('view', $post))->assertOk();

    expect(PostView::sole()->user_id)->toBe($reader->id);
});

test('every visit counts', function () {
    $post = Post::factory()->create();

    $this->get(route('view', $post));
    $this->get(route('view', $post));

    expect(PostView::where('post_id', $post->id)->count())->toBe(2);
});

test('drafts and scheduled posts record no view', function (string $state) {
    $post = Post::factory()->{$state}()->create();

    $this->get(route('view', $post))->assertNotFound();

    expect(PostView::count())->toBe(0);
})->with(['draft', 'scheduled']);
