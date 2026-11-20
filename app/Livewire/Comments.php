<?php

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Comments extends Component
{
    public Post $post;

    #[On('comment-created')]
    #[On('comment-deleted')]
    public function render(): View
    {
        $comments = $this->post->comments()->with('user')->latest()->latest('id')->get();

        return view('livewire.comments', compact('comments'));
    }
}
