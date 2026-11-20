<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\User;
use Spatie\Permission\Models\Role;
use function Pest\Laravel\actingAs;

test('admins can preview a draft without counting a view', function () {
    $admin = User::factory()->create();
    $admin->assignRole(Role::findOrCreate(User::ADMIN_ROLE));
    $draft = Post::factory()->draft()->create();

    actingAs($admin)->get(route('view', $draft))->assertOk();

    expect(PostView::count())->toBe(0);
});

test('readers get a 404 for a draft', function () {
    $draft = Post::factory()->draft()->create();

    actingAs(User::factory()->create())->get(route('view', $draft))->assertNotFound();

    expect(PostView::count())->toBe(0);
});
