<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UpvoteDownvote extends Model
{
    /** @use HasFactory<\Database\Factories\UpvoteDownvoteFactory> */
    use HasFactory;

    protected $fillable = [
        'is_upvote',
        'post_id',
        'user_id'
    ];

    public static function findFor(Post $post, User $user): ?self
    {
        return static::where('post_id', $post->id)->where('user_id', $user->id)->first();
    }

    /**
     * Clicking the same button again takes the vote back; the other button switches it.
     */
    public static function toggle(Post $post, User $user, bool $upvote): void
    {
        $vote = static::findFor($post, $user);

        if ($vote === null) {
            static::create(['is_upvote' => $upvote, 'post_id' => $post->id, 'user_id' => $user->id]);

            return;
        }

        if ($vote->is_upvote === $upvote) {
            $vote->delete();

            return;
        }

        $vote->update(['is_upvote' => $upvote]);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_upvote' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
