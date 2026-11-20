<?php

use App\Livewire\UpvoteDownvote;
use App\Models\Post;
use App\Models\UpvoteDownvote as Vote;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->post = Post::factory()->create();
});

test('guests are sent to the login page', function () {
    Livewire::test(UpvoteDownvote::class, ['post' => $this->post])
        ->call('upvoteDownvote')
        ->assertRedirect(route('login'));

    expect(Vote::count())->toBe(0);
});

test('readers with an unverified email are sent to the verification notice', function () {
    Livewire::actingAs(User::factory()->unverified()->create())
        ->test(UpvoteDownvote::class, ['post' => $this->post])
        ->call('upvoteDownvote')
        ->assertRedirect(route('verification.notice'));

    expect(Vote::count())->toBe(0);
});

test('an upvote is added and removed again on the second click', function () {
    $reader = User::factory()->create();

    $component = Livewire::actingAs($reader)
        ->test(UpvoteDownvote::class, ['post' => $this->post])
        ->call('upvoteDownvote')
        ->assertViewHas('upvotes', 1)
        ->assertViewHas('hasUpvote', true);

    expect(Vote::sole()->only(['user_id', 'post_id', 'is_upvote']))
        ->toBe(['user_id' => $reader->id, 'post_id' => $this->post->id, 'is_upvote' => true]);

    $component->call('upvoteDownvote')
        ->assertViewHas('upvotes', 0)
        ->assertViewHas('hasUpvote', null);

    expect(Vote::count())->toBe(0);
});

test('a downvote replaces an upvote', function () {
    $reader = User::factory()->create();
    Vote::factory()->for($reader)->for($this->post)->create();
    Vote::factory()->for($this->post)->create();

    Livewire::actingAs($reader)
        ->test(UpvoteDownvote::class, ['post' => $this->post])
        ->call('upvoteDownvote', false)
        ->assertViewHas('upvotes', 1)
        ->assertViewHas('downvotes', 1)
        ->assertViewHas('hasUpvote', false);

    expect(Vote::where('user_id', $reader->id)->sole()->is_upvote)->toBeFalse();
});

test('toggling a vote without the component', function () {
    $reader = User::factory()->create();

    Vote::toggle($this->post, $reader, false);
    expect(Vote::findFor($this->post, $reader)?->is_upvote)->toBeFalse();

    Vote::toggle($this->post, $reader, true);
    expect(Vote::findFor($this->post, $reader)?->is_upvote)->toBeTrue();

    Vote::toggle($this->post, $reader, true);
    expect(Vote::findFor($this->post, $reader))->toBeNull();
});
