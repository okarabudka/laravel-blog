<?php

namespace App\Livewire;

use App\Models\Comment;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class CommentItem extends Component
{
    public Comment $comment;

    public function render(): View
    {
        return view('livewire.comment-item');
    }

    public function delete(): void
    {
        $this->authorize('delete', $this->comment);

        $this->comment->delete();
        $this->dispatch('comment-deleted');
    }
}
