<?php

use App\Livewire\CommentCreate;
use App\Livewire\CommentItem;
use App\Livewire\Comments;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

test('readers delete their own comment', function () {
    $comment = Comment::factory()->create();

    Livewire::actingAs($comment->user)
        ->test(CommentItem::class, ['comment' => $comment])
        ->assertSee('Delete')
        ->call('delete')
        ->assertDispatched('comment-deleted');

    expect(Comment::count())->toBe(0);
});

test('deleting a comment of somebody else is forbidden', function () {
    $comment = Comment::factory()->create();

    Livewire::actingAs(User::factory()->create())
        ->test(CommentItem::class, ['comment' => $comment])
        ->assertDontSee('Delete')
        ->call('delete')
        ->assertForbidden();

    expect(Comment::count())->toBe(1);
});

test('a comment shows its author and date', function () {
    $comment = Comment::factory()
        ->for(User::factory()->state(['name' => 'Ada Reader']))
        ->create(['created_at' => now()->subHours(2)]);

    Livewire::test(CommentItem::class, ['comment' => $comment])
        ->assertSee('Ada Reader')
        ->assertSee('2 hours ago');
});

test('the newest comment comes first', function () {
    $post = Post::factory()->create();
    Comment::factory()->for($post)->create(['comment' => 'Older comment', 'created_at' => now()->subDay()]);
    Comment::factory()->for($post)->create(['comment' => 'Newer comment']);

    Livewire::test(Comments::class, ['post' => $post])
        ->assertSeeInOrder(['Newer comment', 'Older comment']);
});

test('readers with an unverified email are sent to the verification notice', function () {
    Livewire::actingAs(User::factory()->unverified()->create())
        ->test(CommentCreate::class, ['post' => Post::factory()->create()])
        ->set('comment', 'Hello')
        ->call('createComment')
        ->assertRedirect(route('verification.notice'));

    expect(Comment::count())->toBe(0);
});
