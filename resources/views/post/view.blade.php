<x-app-layout :meta-title="$post->meta_title ?: $post->title" :meta-description="$post->meta_description">
    <div class="layout">
        <div>
            <article class="post">
                <img class="post__image" src="{{ $post->getThumbnail() }}" alt="">

                <div class="post__body">
                    <div class="badges">
                        @foreach($post->categories as $category)
                            <a href="{{ route('by-category', $category) }}" class="badge">{{ $category->title }}</a>
                        @endforeach
                    </div>

                    <h1 class="post__title">{{ $post->title }}</h1>

                    <p class="post-meta">
                        By <strong>{{ $post->user->name }}</strong> · {{ $post->getFormattedDate() }} · {{ $post->human_read_time }}
                    </p>

                    <div class="prose">{!! $post->body !!}</div>

                    <div class="post__footer">
                        <span class="muted small">Did you like this post?</span>
                        <livewire:upvote-downvote :post="$post"/>
                    </div>
                </div>
            </article>

            <nav class="post-nav">
                <div>
                    @if($prev)
                        <a href="{{ route('view', $prev) }}">
                            <span class="post-nav__label"><x-heroicon-o-arrow-left class="icon"/> Previous</span>
                            {{ Str::words($prev->title, 8) }}
                        </a>
                    @endif
                </div>
                <div>
                    @if($next)
                        <a href="{{ route('view', $next) }}" class="post-nav__next">
                            <span class="post-nav__label">Next <x-heroicon-o-arrow-right class="icon"/></span>
                            {{ Str::words($next->title, 8) }}
                        </a>
                    @endif
                </div>
            </nav>

            <livewire:comments :post="$post"/>
        </div>

        <x-sidebar/>
    </div>
</x-app-layout>
