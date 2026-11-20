<?php

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature', 'Unit');

/**
 * Typed access to a variable passed to the rendered view.
 *
 * @template T of object
 *
 * @param  TestResponse<Response>  $response
 * @param  class-string<T>  $type
 * @return T
 */
function viewVariable(TestResponse $response, string $key, string $type): object
{
    $value = $response->viewData($key);

    expect($value)->toBeInstanceOf($type);
    assert($value instanceof $type);

    return $value;
}

function postInState(string $state): Post
{
    $factory = match ($state) {
        'published' => Post::factory()->published(),
        'draft' => Post::factory()->draft(),
        'scheduled' => Post::factory()->scheduled(),
        default => throw new InvalidArgumentException("Unknown post state {$state}"),
    };

    return $factory->create();
}
