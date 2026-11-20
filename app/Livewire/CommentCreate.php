<?php

namespace App\Livewire;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CommentCreate extends Component
{
    #[Validate('required|string|max:2000')]
    public string $comment = '';

    public Post $post;

    public function render(): View
    {
        return view('livewire.comment-create');
    }

    public function createComment(): void
    {
        $user = auth()->user();

        if (! $user) {
            $this->redirectRoute('login');

            return;
        }

        // The policy only refuses readers who have not verified their email yet.
        if ($user->cannot('create', Comment::class)) {
            $this->redirectRoute('verification.notice');

            return;
        }

        $this->validate();

        $this->post->comments()->create([
            'comment' => $this->comment,
            'user_id' => $user->id,
        ]);

        $this->reset('comment');
        $this->dispatch('comment-created');
    }
}
