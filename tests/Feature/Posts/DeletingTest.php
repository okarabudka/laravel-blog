<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use App\Models\UpvoteDownvote;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('a post with comments, views and votes can be deleted', function () {
    $post = Post::factory()
        ->has(Comment::factory()->count(2))
        ->hasViews(3)
        ->hasVotes(1)
        ->create();

    $post->delete();

    expect(Comment::count())->toBe(0)
        ->and(PostView::count())->toBe(0)
        ->and(UpvoteDownvote::count())->toBe(0);
});

test('readers with comments and views can delete their account', function () {
    $reader = User::factory()->create();
    $post = Post::factory()->create();
    Comment::factory()->for($reader)->for($post)->create();
    PostView::factory()->for($post)->create(['user_id' => $reader->id]);

    actingAs($reader)
        ->delete('/profile', ['password' => 'password'])
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    expect($reader->fresh())->toBeNull()
        ->and(Comment::count())->toBe(0)
        ->and(PostView::sole()->user_id)->toBeNull();
});
