<?php

use App\Models\Post;
use App\Models\PostView;
use App\Models\TextWidget;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;

test('a published post is shown and its view is recorded', function () {
    $post = Post::factory()->create();

    $this->get(route('view', $post))->assertOk()->assertSee($post->title);

    expect(PostView::where('post_id', $post->id)->count())->toBe(1);
});

test('a draft post is not found', function () {
    $post = Post::factory()->draft()->create();

    $this->get(route('view', $post))->assertNotFound();
});

test('the about page shows its text widget', function () {
    TextWidget::factory()->create(['key' => 'about-us', 'title' => 'About this blog']);

    $this->get(route('about'))->assertOk()->assertSee('About this blog');
});

test('the admin panel is open to admins only', function () {
    $this->seed([RoleSeeder::class, UserSeeder::class]);
    $admin = User::where('email', 'admin@example.com')->firstOrFail();

    $this->actingAs($admin)->get('/admin')->assertOk();
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});
