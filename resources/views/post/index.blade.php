<x-app-layout :meta-title="$category->title" :meta-description="'Posts in '.$category->title">
    <div class="layout">
        <section>
            <h1 class="section-title">{{ $category->title }}</h1>

            @if($posts->isEmpty())
                <div class="empty">No posts in this category yet.</div>
            @else
                <div class="post-grid">
                    @foreach($posts as $post)
                        <x-post-item :post="$post"/>
                    @endforeach
                </div>

                {{ $posts->links() }}
            @endif
        </section>

        <x-sidebar/>
    </div>
</x-app-layout>
