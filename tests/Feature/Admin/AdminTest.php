<?php

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\ViewPost;
use App\Filament\Resources\Posts\Widgets\PostOverview;
use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    $this->admin = User::factory()->create();
    $this->admin->assignRole(Role::findOrCreate('admin'));
});

test('admins can open the panel and readers cannot', function () {
    $this->actingAs($this->admin)->get('/admin')->assertOk();
    $this->actingAs(User::factory()->create())->get('/admin')->assertForbidden();
});

test('guests are sent to the panel login', function () {
    $this->get('/admin')->assertRedirect('/admin/login');
});

test('every resource list opens for admins', function (string $path) {
    $this->actingAs($this->admin)->get($path)->assertOk();
})->with(['/admin/posts', '/admin/categories', '/admin/text-widgets', '/admin/users']);

test('admins create a post with a slug from the title', function () {
    $category = Category::factory()->create();

    Livewire::actingAs($this->admin)
        ->test(CreatePost::class)
        ->fillForm([
            'title' => 'Hello from the admin panel',
            'body' => '<p>Body</p>',
            'active' => true,
            'published_at' => now()->subHour(),
            'categories' => [$category->id],
        ])
        ->assertSchemaStateSet(['slug' => 'hello-from-the-admin-panel'])
        ->call('create')
        ->assertHasNoFormErrors();

    $post = Post::sole();
    expect($post->slug)->toBe('hello-from-the-admin-panel')
        ->and($post->user_id)->toBe($this->admin->id)
        ->and($post->categories->pluck('id')->all())->toBe([$category->id]);
});

test('the post view page shows views and votes', function () {
    $post = Post::factory()->hasViews(4)->create();

    $this->actingAs($this->admin)->get(ViewPost::getUrl(['record' => $post]))->assertOk();

    Livewire::test(PostOverview::class, ['record' => $post])
        ->assertSeeInOrder(['Views', '4', 'Upvotes', '0', 'Downvotes', '0']);
});

test('admins create another admin with a verified email', function () {
    Livewire::actingAs($this->admin)
        ->test(CreateUser::class)
        ->fillForm([
            'name' => 'Second Admin',
            'email' => 'second@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $user = User::where('email', 'second@example.com')->sole();
    expect($user->hasRole('admin'))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('a-long-password', $user->password))->toBeTrue();
});

test('creating a user needs a matching password confirmation', function () {
    Livewire::actingAs($this->admin)
        ->test(CreateUser::class)
        ->fillForm([
            'name' => 'Second Admin',
            'email' => 'second@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'something-else',
        ])
        ->call('create')
        ->assertHasFormErrors(['password' => 'confirmed']);
});
