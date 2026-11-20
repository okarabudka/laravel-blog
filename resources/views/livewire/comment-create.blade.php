<form class="comment-form" wire:submit="createComment">
    <label for="comment" class="sr-only">Comment</label>
    <textarea id="comment" wire:model="comment" class="input" rows="3" placeholder="Leave a comment"></textarea>
    @error('comment')
        <ul class="errors"><li>{{ $message }}</li></ul>
    @enderror

    <div>
        <button type="submit" class="btn btn-primary">Publish comment</button>
    </div>
</form>
