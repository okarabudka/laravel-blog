<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function home(): View
    {
        //Latest posts
        $latestPost = Post::published()
            ->orderBy('published_at', 'desc')
            ->limit(1)
            ->first();

        //Show the most popular 3 posts based on upvotes
        $popularPosts = Post::query()
            ->leftJoin('upvote_downvotes', 'posts.id', 'upvote_downvotes.post_id')
            ->select('posts.*', DB::raw('COUNT(upvote_downvotes.id) as upvote_count'))
            ->where(function ($query) {
                $query->whereNull('upvote_downvotes.is_upvote')
                    ->orWhere('upvote_downvotes.is_upvote', '=', 1);
            })
            ->published()
            ->orderByDesc('upvote_count')
            ->groupBy('posts.id')
            ->limit(3)
            ->get();

        //If authorized - Show recommended posts based on user upvotes
        //If not authorized - Popular posts based on views
        $user = auth()->user();

        if ($user) {
            $leftJoin = '(
                    SELECT cp.category_id, cp.post_id
                    FROM upvote_downvotes
                    JOIN category_post cp ON upvote_downvotes.post_id = cp.post_id
                    WHERE upvote_downvotes.is_upvote = 1 and upvote_downvotes.user_id = ?
                ) as t';
            $recommendedPosts = Post::query()
                ->leftJoin('category_post as cp', 'posts.id', 'cp.post_id')
                ->leftJoin(DB::raw($leftJoin), function ($join) {
                    $join->on('t.category_id', '=', 'cp.category_id')->on('t.post_id', '<>', 'cp.post_id');
                })
                ->select('posts.*')
                ->where('posts.id', '<>', DB::raw('t.post_id'))
                ->setBindings([$user->id])
                ->get();
        } else {
            $recommendedPosts = Post::query()
                ->leftJoin('post_views', 'posts.id', 'post_views.post_id')
                ->select('posts.*', DB::raw('COUNT(post_views.id) as view_count'))
                ->published()
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(5)
                ->get();
        }

        return view('home', compact('latestPost', 'popularPosts', 'recommendedPosts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post, Request $request)
    {
        if (! $post->isPublished()) {
            throw new NotFoundHttpException();
        }

        $prev = Post::query()
            ->published()
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->first();

        $next = Post::query()
            ->published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->first();

        $user = $request->user();

        PostView::create([
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'post_id' => $post->id,
            'user_id' => $user?->id
        ]);

        return view('post.view', compact('post', 'prev', 'next'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function byCategory(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with(['user', 'categories'])
            ->orderByDesc('published_at')
            ->paginate(10);

        return view('post.index', compact('category', 'posts'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
