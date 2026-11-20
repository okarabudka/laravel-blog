<?php

use App\Livewire\CommentCreate;
use App\Livewire\Comments;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

function commentablePost(): Post
{
    // The old code saved every comment as user 1, so make sure that is somebody else.
    User::factory()->create();

    return Post::factory()->create();
}

test('guests are sent to the login page', function () {
    $post = commentablePost();

    Livewire::test(CommentCreate::class, ['post' => $post])
        ->set('comment', 'Hello')
        ->call('createComment')
        ->assertRedirect(route('login'));

    expect(Comment::count())->toBe(0);
});

test('a comment is saved as the signed-in reader', function () {
    $post = commentablePost();

    $reader = User::factory()->create();

    Livewire::actingAs($reader)
        ->test(CommentCreate::class, ['post' => $post])
        ->set('comment', 'Great read, thanks!')
        ->call('createComment')
        ->assertHasNoErrors();

    $comment = Comment::sole();
    expect($comment->user_id)->toBe($reader->id)
        ->and($comment->post_id)->toBe($post->id)
        ->and($comment->comment)->toBe('Great read, thanks!');
});

test('an empty comment is rejected', function () {
    $post = commentablePost();

    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $post])
        ->set('comment', '   ')
        ->call('createComment')
        ->assertHasErrors(['comment' => 'required']);

    expect(Comment::count())->toBe(0);
});

test('a comment longer than 2000 characters is rejected', function () {
    $post = commentablePost();

    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $post])
        ->set('comment', str_repeat('a', 2001))
        ->call('createComment')
        ->assertHasErrors(['comment' => 'max']);
});

test('the form is cleared and the list is told to refresh', function () {
    $post = commentablePost();

    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $post])
        ->set('comment', 'First!')
        ->call('createComment')
        ->assertSet('comment', '')
        ->assertDispatched('comment-created');
});

test('the comment list refreshes when a comment is created', function () {
    $post = commentablePost();

    $list = Livewire::test(Comments::class, ['post' => $post])->assertDontSee('Fresh comment');

    Comment::factory()->for($post)->create(['comment' => 'Fresh comment']);

    $list->dispatch('comment-created');
    $list->assertSee('Fresh comment');
});
