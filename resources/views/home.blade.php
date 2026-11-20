<x-app-layout meta-description="My personal blog page">
    @if(! $latestPost)
        <div class="empty">No posts yet. Write the first one in the admin panel.</div>
    @else
        <div class="home-top">
            <section>
                <h2 class="section-title">Latest post</h2>
                <x-post-item :post="$latestPost" class="post-card--featured"/>
            </section>

            <section>
                <h2 class="section-title">Popular posts</h2>
                <div class="post-stack">
                    @foreach($popularPosts as $post)
                        <x-post-item :post="$post" :excerpt="false" class="post-card--compact"/>
                    @endforeach
                </div>
            </section>
        </div>

        <section class="section">
            <h2 class="section-title">Recommended posts</h2>
            <div class="post-grid">
                @foreach($recommendedPosts as $post)
                    <x-post-item :post="$post"/>
                @endforeach
            </div>
        </section>
    @endif
</x-app-layout>
