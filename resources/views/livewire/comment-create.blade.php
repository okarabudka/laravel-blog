<div class="comment-form">
    <input wire:model="comment" type="text" class="input" placeholder="Leave a comment">

    <div>
        <button wire:click="createComment" type="submit" class="btn btn-primary">Publish comment</button>
    </div>
</div>
