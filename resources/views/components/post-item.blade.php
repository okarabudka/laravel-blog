@props(['post', 'excerpt' => true])

<article {{ $attributes->merge(['class' => 'post-card']) }}>
    <a href="{{ route('view', $post) }}" class="post-card__image">
        <img src="{{ $post->getThumbnail() }}" alt="" loading="lazy">
    </a>

    <div class="post-card__body">
        <div class="badges">
            @foreach($post->categories as $category)
                <a href="{{ route('by-category', $category) }}" class="badge">{{ $category->title }}</a>
            @endforeach
        </div>

        <a href="{{ route('view', $post) }}" class="post-card__title">{{ $post->title }}</a>

        <p class="post-meta">
            By <strong>{{ $post->user->name }}</strong> · {{ $post->getFormattedDate() }} · {{ $post->human_read_time }}
        </p>

        @if($excerpt)
            <p class="post-card__excerpt">{{ $post->shortBody() }}</p>

            <a href="{{ route('view', $post) }}" class="read-more">
                Continue reading <x-heroicon-o-arrow-right class="icon"/>
            </a>
        @endif
    </div>
</article>
