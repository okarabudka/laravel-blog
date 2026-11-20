<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    private const int WORDS_PER_MINUTE = 200;

    protected $fillable = ['title', 'slug', 'thumbnail', 'body', 'active', 'published_at', 'user_id', 'meta_title', 'meta_description'];

    protected $casts = [
        'active' => 'boolean',
        'published_at' => 'datetime',
    ];

    /**
     * Active posts whose publication time has passed.
     *
     * @param  Builder<self>  $query
     */
    #[Scope]
    protected function published(Builder $query): void
    {
        $query->where('active', true)
            ->where('published_at', '<=', now());
    }

    public function isPublished(): bool
    {
        return $this->active && $this->published_at !== null && $this->published_at->lte(now());
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function shortBody(): string
    {
        return Str::words(strip_tags($this->body), 30);
    }

    public function getFormattedDate(): string
    {
        return $this->published_at?->format('F jS Y') ?? '';
    }

    public function getThumbnail(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        if (str_starts_with($this->thumbnail, 'http')) {
            return $this->thumbnail;
        }

        return '/storage/'.$this->thumbnail;
    }

    /**
     * Reading time at 200 words per minute, e.g. "3 mins, 512 words".
     *
     * @return Attribute<string, never>
     */
    protected function humanReadTime(): Attribute
    {
        return Attribute::get(fn (): string => $this->readTime());
    }

    private function readTime(): string
    {
        $words = Str::wordCount(strip_tags($this->body));
        $minutes = (int) ceil($words / self::WORDS_PER_MINUTE);

        return $minutes.' '.Str::plural('min', $minutes).', '.$words.' '.Str::plural('word', $words);
    }

    /**
     * @return HasMany<UpvoteDownvote, $this>
     */
    public function votes(): HasMany
    {
        return $this->hasMany(UpvoteDownvote::class);
    }

    /**
     * @return HasMany<PostView, $this>
     */
    public function views(): HasMany
    {
        return $this->hasMany(PostView::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
