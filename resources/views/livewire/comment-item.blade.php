<article class="comment">
    <div class="comment__meta">
        <strong>{{ $comment->user->name }}</strong>
        <time datetime="{{ $comment->created_at?->toIso8601String() }}">{{ $comment->created_at?->diffForHumans() }}</time>

        @can('delete', $comment)
            <span class="comment__actions">
                <button type="button" class="link small" wire:click="delete"
                        wire:confirm="Delete this comment?">Delete</button>
            </span>
        @endcan
    </div>
    <p class="comment__text">{{ $comment->comment }}</p>
</article>
