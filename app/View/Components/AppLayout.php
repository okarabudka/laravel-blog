<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\View\Component;
use Illuminate\View\View;

class AppLayout extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public ?string $metaTitle = null, public ?string $metaDescription = null)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $categories = Category::withPublishedPosts()->get();

        return view('layouts.app', compact('categories'));
    }
}
