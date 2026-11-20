<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\UpvoteDownvote;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use function Pest\Laravel\actingAs;

/**
 * @return array{User, Category}
 */
function readerWhoLikedACategory(): array
{
    $reader = User::factory()->create();
    $category = Category::factory()->create();
    $liked = Post::factory()->hasAttached($category)->create();
    UpvoteDownvote::factory()->for($reader)->for($liked)->create();

    return [$reader, $category];
}

test('readers get published posts from the categories they upvoted', function () {
    [$reader, $category] = readerWhoLikedACategory();
    $match = Post::factory()->hasAttached($category)->create();
    Post::factory()->hasAttached(Category::factory())->create();

    $recommended = viewVariable(actingAs($reader)->get(route('home')), 'recommendedPosts', Collection::class);

    expect($recommended->pluck('id')->all())->toBe([$match->id]);
});

test('drafts and scheduled posts are not recommended', function () {
    [$reader, $category] = readerWhoLikedACategory();
    Post::factory()->draft()->hasAttached($category)->create();
    Post::factory()->scheduled()->hasAttached($category)->create();

    $recommended = viewVariable(actingAs($reader)->get(route('home')), 'recommendedPosts', Collection::class);

    expect($recommended)->toBeEmpty();
});

test('posts the reader already voted on are not recommended', function () {
    [$reader, $category] = readerWhoLikedACategory();
    $downvoted = Post::factory()->hasAttached($category)->create();
    UpvoteDownvote::factory()->downvote()->for($reader)->for($downvoted)->create();

    $recommended = viewVariable(actingAs($reader)->get(route('home')), 'recommendedPosts', Collection::class);

    expect($recommended)->toBeEmpty();
});

test('recommendations have no duplicates and at most five posts', function () {
    $reader = User::factory()->create();
    $category = Category::factory()->create();
    Post::factory()->count(2)->hasAttached($category)->create()
        ->each(fn (Post $post) => UpvoteDownvote::factory()->for($reader)->for($post)->create());
    Post::factory()->count(7)->hasAttached($category)->create();

    $recommended = viewVariable(actingAs($reader)->get(route('home')), 'recommendedPosts', Collection::class);

    expect($recommended)->toHaveCount(5)
        ->and($recommended->pluck('id')->unique())->toHaveCount(5);
});

test('readers without votes get the most viewed posts', function () {
    $reader = User::factory()->create();
    $popular = Post::factory()->hasViews(3)->create();

    $recommended = viewVariable(actingAs($reader)->get(route('home')), 'recommendedPosts', Collection::class);

    expect($recommended->first()?->is($popular))->toBeTrue();
});
