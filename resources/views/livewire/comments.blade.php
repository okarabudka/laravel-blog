<section class="comments">
    <h2 class="section-title">Comments</h2>

    @foreach($comments as $comment)
        <livewire:comment-item :comment="$comment" :key="$comment->id"/>
    @endforeach

    <livewire:comment-create :post="$post"/>
</section>
