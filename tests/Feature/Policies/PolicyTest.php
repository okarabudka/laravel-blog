<?php

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

function userWithRole(string $who): ?User
{
    return match ($who) {
        'guest' => null,
        'reader' => User::factory()->create(),
        'unverified' => User::factory()->unverified()->create(),
        'admin' => tap(User::factory()->create(), fn (User $user) => $user->assignRole(Role::findOrCreate(User::ADMIN_ROLE))),
    };
}

test('policies are discovered for their models', function () {
    expect(Gate::getPolicyFor(Post::class))->toBeInstanceOf(PostPolicy::class)
        ->and(Gate::getPolicyFor(Comment::class))->toBeInstanceOf(CommentPolicy::class);
});

test('who may view a post', function (string $who, string $state, bool $allowed) {
    $post = Post::factory()->{$state}()->create();

    expect(Gate::forUser(userWithRole($who))->allows('view', $post))->toBe($allowed);
})->with([
    ['guest', 'published', true],
    ['guest', 'draft', false],
    ['guest', 'scheduled', false],
    ['reader', 'published', true],
    ['reader', 'draft', false],
    ['admin', 'draft', true],
    ['admin', 'scheduled', true],
]);

test('who may write a comment', function (string $who, bool $allowed) {
    expect(Gate::forUser(userWithRole($who))->allows('create', Comment::class))->toBe($allowed);
})->with([
    ['guest', false],
    ['unverified', false],
    ['reader', true],
    ['admin', true],
]);

test('who may delete a comment', function (string $who, bool $own, bool $allowed) {
    $user = userWithRole($who);
    $comment = Comment::factory()->create($own && $user ? ['user_id' => $user->id] : []);

    expect(Gate::forUser($user)->allows('delete', $comment))->toBe($allowed)
        ->and(Gate::forUser($user)->allows('update', $comment))->toBe($allowed);
})->with([
    ['guest', false, false],
    ['reader', true, true],
    ['reader', false, false],
    ['admin', false, true],
]);
