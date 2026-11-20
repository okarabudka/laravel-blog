<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\UpvoteDownvote;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use function Pest\Laravel\get;

test('the home page renders on an empty database', function () {
    get(route('home'))
        ->assertOk()
        ->assertSee('No posts yet');
});

test('popular posts are ordered by upvotes and ignore downvotes', function () {
    $first = Post::factory()->hasVotes(3)->create();
    $second = Post::factory()->hasVotes(2)->has(UpvoteDownvote::factory()->downvote()->count(4), 'votes')->create();
    $onlyDownvoted = Post::factory()->has(UpvoteDownvote::factory()->downvote(), 'votes')->create();

    $popular = viewVariable(get(route('home'))->assertOk(), 'popularPosts', Collection::class);

    expect($popular->pluck('id')->all())->toBe([$first->id, $second->id, $onlyDownvoted->id])
        ->and($popular->pluck('upvote_count')->all())->toBe([3, 2, 0]);
});

test('guests get the most viewed posts as recommendations', function () {
    $most = Post::factory()->hasViews(5)->create();
    $less = Post::factory()->hasViews(2)->create();
    $none = Post::factory()->create();

    $recommended = viewVariable(get(route('home'))->assertOk(), 'recommendedPosts', Collection::class);

    expect($recommended->pluck('id')->all())->toBe([$most->id, $less->id, $none->id]);
});

test('the navigation lists categories by their number of published posts', function () {
    $small = Category::factory()->create(['title' => 'Small topic']);
    $big = Category::factory()->create(['title' => 'Big topic']);
    Category::factory()->create(['title' => 'Empty topic']);
    Post::factory()->hasAttached($small)->create();
    Post::factory()->count(2)->hasAttached($big)->create();
    Post::factory()->draft()->count(3)->hasAttached($small)->create();

    $html = get(route('home'))->assertOk()->getContent();
    $navigation = Str::betweenFirst((string) $html, 'class="topics-list"', '</div>');

    expect($navigation)->toMatch('/Big topic.*Small topic/s')
        ->and(str_contains($navigation, 'Empty topic'))->toBeFalse();
});
