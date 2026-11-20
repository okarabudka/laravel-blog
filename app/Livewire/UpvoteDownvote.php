<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\UpvoteDownvote as Vote;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class UpvoteDownvote extends Component
{
    public Post $post;

    public function render(): View
    {
        $upvotes = $this->post->votes()->where('is_upvote', true)->count();
        $downvotes = $this->post->votes()->where('is_upvote', false)->count();

        $user = auth()->user();
        $vote = $user instanceof User ? Vote::findFor($this->post, $user) : null;
        $hasUpvote = $vote?->is_upvote;

        return view('livewire.upvote-downvote', compact('upvotes', 'downvotes', 'hasUpvote'));
    }

    public function upvoteDownvote(bool $upvote = true): void
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            $this->redirectRoute('login');

            return;
        }

        if (! $user->hasVerifiedEmail()) {
            $this->redirectRoute('verification.notice');

            return;
        }

        Vote::toggle($this->post, $user, $upvote);
    }
}
