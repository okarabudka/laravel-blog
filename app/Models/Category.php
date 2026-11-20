<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug'];

    /**
     * Categories that have published posts, with their number, the largest first.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function withPublishedPosts(Builder $query): void
    {
        $published = fn (Builder $posts) => $posts->published();

        $query->whereHas('posts', $published)
            ->withCount(['posts' => $published])
            ->orderByDesc('posts_count')
            ->orderBy('title');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class);
    }
}
