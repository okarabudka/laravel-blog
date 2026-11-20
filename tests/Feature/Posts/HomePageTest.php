<?php

test('the home page renders on an empty database', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('No posts yet');
});
