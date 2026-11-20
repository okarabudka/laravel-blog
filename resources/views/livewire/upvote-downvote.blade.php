<div class="votes">
    <button type="button" wire:click="upvoteDownvote()" @class(['vote-btn', 'is-selected' => $hasUpvote === true])
            aria-label="Upvote">
        <x-heroicon-o-hand-thumb-up class="icon"/> {{ $upvotes }}
    </button>
    <button type="button" wire:click="upvoteDownvote(false)" @class(['vote-btn', 'is-selected' => $hasUpvote === false])
            aria-label="Downvote">
        <x-heroicon-o-hand-thumb-down class="icon"/> {{ $downvotes }}
    </button>
</div>
