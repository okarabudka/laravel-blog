<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /** Length of post_views.user_agent. */
    private const int USER_AGENT_LENGTH = 255;

    public function home(): View
    {
        $latestPost = Post::published()
            ->orderByDesc('published_at')
            ->first();

        $popularPosts = Post::published()
            ->withCount(['votes as upvote_count' => fn (Builder $votes) => $votes->where('is_upvote', true)])
            ->orderByDesc('upvote_count')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        // Readers get posts from the categories they upvoted; guests and readers
        // without such posts get the most viewed ones.
        $user = auth()->user();
        $user = $user instanceof User ? $user : null;
        $recommendedPosts = $user ? $this->recommendedFor($user) : new Collection;

        if ($recommendedPosts->isEmpty()) {
            $recommendedPosts = Post::published()
                ->withCount('views')
                ->when($user, fn (Builder $posts, User $user) => $this->notVotedBy($posts, $user))
                ->orderByDesc('views_count')
                ->orderByDesc('published_at')
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

    public function show(Post $post, Request $request): View
    {
        Gate::authorize('view', $post);

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

        // Admin previews of drafts are not reader views.
        if ($post->isPublished()) {
            PostView::create([
                'ip_address' => $request->ip(),
                'user_agent' => Str::limit((string) $request->userAgent(), self::USER_AGENT_LENGTH, ''),
                'post_id' => $post->id,
                'user_id' => $request->user()?->id,
            ]);
        }

        return view('post.view', compact('post', 'prev', 'next'));
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
}
