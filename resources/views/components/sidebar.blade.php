@php use App\Models\TextWidget; @endphp
<aside class="sidebar">
    <div class="card">
        <h3 class="widget-title">All categories</h3>
        <ul class="category-list">
            @foreach($categories as $category)
                <li>
                    <a href="{{ route('by-category', $category) }}"
                       @class(['is-active' => request('category')?->slug === $category->slug])>
                        <span>{{ $category->title }}</span> <span>{{ $category->total }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="card">
        <h3 class="widget-title">{{ TextWidget::getTitle('about-us-sidebar') }}</h3>
        <div class="muted">{!! TextWidget::getContent('about-us-sidebar') !!}</div>
        <a href="{{ route('about') }}" class="btn btn-primary btn-block">Get to know us</a>
    </div>
</aside>
