<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
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

        // Readers get posts from the categories they upvoted; guests and readers
        // without such posts get the most viewed ones.
        $user = auth()->user();
        $user = $user instanceof User ? $user : null;
        $recommendedPosts = $user ? $this->recommendedFor($user) : new Collection;

        if ($recommendedPosts->isEmpty()) {
            $recommendedPosts = Post::query()
                ->leftJoin('post_views', 'posts.id', 'post_views.post_id')
                ->select('posts.*', DB::raw('COUNT(post_views.id) as view_count'))
                ->published()
                ->when($user, fn (Builder $posts, User $user) => $this->notVotedBy($posts, $user))
                ->orderByDesc('view_count')
                ->groupBy('posts.id')
                ->limit(5)
                ->get();
        }

        return view('home', compact('latestPost', 'popularPosts', 'recommendedPosts'));
    }

    /**
     * @return Collection<int, Post>
     */
    private function recommendedFor(User $user): Collection
    {
        return Post::published()
            ->whereHas('categories.posts.votes', fn (Builder $votes) => $votes
                ->where('user_id', $user->id)
                ->where('is_upvote', true))
            ->tap(fn (Builder $posts) => $this->notVotedBy($posts, $user))
            ->orderByDesc('published_at')
            ->limit(5)
            ->get();
    }

    /**
     * @param  Builder<Post>  $posts
     * @return Builder<Post>
     */
    private function notVotedBy(Builder $posts, User $user): Builder
    {
        return $posts->whereDoesntHave('votes', fn (Builder $votes) => $votes->where('user_id', $user->id));
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
