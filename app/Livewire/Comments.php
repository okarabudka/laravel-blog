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
    public function render(): View
    {
        $comments = $this->post->comments()->get();

        return view('livewire.comments', compact('comments'));
    }
}
