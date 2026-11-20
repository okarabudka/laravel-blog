<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use App\Models\UpvoteDownvote;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

/**
 * Votes, views and comments in plausible proportions, so the home page rankings have something to rank.
 */
class EngagementSeeder extends Seeder
{
    private const array COMMENTS = [
        'Thanks, this saved me an afternoon.',
        'We ran into exactly this last month. The test-first approach is what finally fixed it for us.',
        'Clear and to the point. Would love a follow-up with a bigger example.',
        'I did not know about this one. Bookmarked.',
        'How does this play with queues running on several servers?',
        'Great write-up. The before and after comparison helps a lot.',
        'Tried it on our project today, works as described.',
        'Small nitpick: the second snippet needs an import, otherwise spot on.',
        'This is the kind of post I wish I had found two years ago.',
        'Agree with most of it, although we ended up with a slightly different setup.',
    ];

    public function run(): void
    {
        if (UpvoteDownvote::exists() || PostView::exists() || Comment::exists()) {
            return;
        }

        fake()->seed(2026);

        $readers = User::where('email', 'like', 'reader%@example.com')->get()->values()->all();
        $readerIds = array_map(fn (User $reader) => $reader->id, $readers);

        foreach (Post::published()->get() as $post) {
            $this->votes($post, $readers);
            $this->views($post, $readerIds);
            $this->comments($post, $readers);
        }
    }

    /**
     * @param  array<int, User>  $readers
     */
    private function votes(Post $post, array $readers): void
    {
        foreach ($readers as $reader) {
            if (fake()->boolean(45)) {
                UpvoteDownvote::create([
                    'post_id' => $post->id,
                    'user_id' => $reader->id,
                    'is_upvote' => fake()->boolean(85),
                ]);
            }
        }
    }

    /**
     * @param  array<int, int>  $readerIds
     */
    private function views(Post $post, array $readerIds): void
    {
        $rows = array_map(fn () => [
            'post_id' => $post->id,
            'user_id' => fake()->boolean(25) ? fake()->randomElement($readerIds) : null,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => $viewedAt = fake()->dateTimeBetween($post->published_at ?? 'now', 'now'),
            'updated_at' => $viewedAt,
        ], range(1, fake()->numberBetween(5, 120)));

        PostView::insert($rows);
    }

    /**
     * @param  array<int, User>  $readers
     */
    private function comments(Post $post, array $readers): void
    {
        $commenters = collect($readers)->shuffle()->take(fake()->numberBetween(0, 3));

        foreach ($commenters as $reader) {
            $comment = new Comment([
                'post_id' => $post->id,
                'user_id' => $reader->id,
                'comment' => fake()->randomElement(self::COMMENTS),
            ]);
            $comment->created_at = $comment->updated_at = Date::instance(fake()->dateTimeBetween($post->published_at ?? 'now', 'now'));
            $comment->save();
        }
    }
}
