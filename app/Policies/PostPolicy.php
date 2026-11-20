<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * Admins manage every post, including drafts, through the admin panel.
     */
    public function before(User $user): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    /**
     * Unpublished posts answer 404 rather than 403, so drafts stay invisible.
     */
    public function view(?User $user, Post $post): Response
    {
        return $post->isPublished() ? Response::allow() : Response::denyAsNotFound();
    }
}
