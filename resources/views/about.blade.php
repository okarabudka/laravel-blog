<x-app-layout meta-title="About us" meta-description="About this blog">
    <div class="layout">
        <article class="post">
            @if($widget->image)
                <img class="post__image" src="/storage/{{ $widget->image }}" alt="">
            @endif
            <div class="post__body">
                <h1 class="post__title">{{ $widget->title }}</h1>
                <div class="prose">{!! $widget->content !!}</div>
            </div>
        </article>

        <x-sidebar/>
    </div>
</x-app-layout>
