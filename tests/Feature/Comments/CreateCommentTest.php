<?php

use App\Livewire\CommentCreate;
use App\Livewire\Comments;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // The old code saved every comment as user 1, so make sure that is somebody else.
    User::factory()->create();
    $this->post = Post::factory()->create();
});

test('guests are sent to the login page', function () {
    Livewire::test(CommentCreate::class, ['post' => $this->post])
        ->set('comment', 'Hello')
        ->call('createComment')
        ->assertRedirect(route('login'));

    expect(Comment::count())->toBe(0);
});

test('a comment is saved as the signed-in reader', function () {
    $reader = User::factory()->create();

    Livewire::actingAs($reader)
        ->test(CommentCreate::class, ['post' => $this->post])
        ->set('comment', 'Great read, thanks!')
        ->call('createComment')
        ->assertHasNoErrors();

    $comment = Comment::sole();
    expect($comment->user_id)->toBe($reader->id)
        ->and($comment->post_id)->toBe($this->post->id)
        ->and($comment->comment)->toBe('Great read, thanks!');
});

test('an empty comment is rejected', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $this->post])
        ->set('comment', '   ')
        ->call('createComment')
        ->assertHasErrors(['comment' => 'required']);

    expect(Comment::count())->toBe(0);
});

test('a comment longer than 2000 characters is rejected', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $this->post])
        ->set('comment', str_repeat('a', 2001))
        ->call('createComment')
        ->assertHasErrors(['comment' => 'max']);
});

test('the form is cleared and the list is told to refresh', function () {
    Livewire::actingAs(User::factory()->create())
        ->test(CommentCreate::class, ['post' => $this->post])
        ->set('comment', 'First!')
        ->call('createComment')
        ->assertSet('comment', '')
        ->assertDispatched('comment-created');
});

test('the comment list refreshes when a comment is created', function () {
    $list = Livewire::test(Comments::class, ['post' => $this->post])->assertDontSee('Fresh comment');

    Comment::factory()->for($this->post)->create(['comment' => 'Fresh comment']);

    $list->dispatch('comment-created')->assertSee('Fresh comment');
});
