<?php

namespace App\Policies;

use App\Enums\PostStatusEnum;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PostPolicy
{

    public function view(User $user, Post $post): bool
    {
        return $user->id == $post->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Post $post): bool
    {
        return $user->id == $post->user_id;
    }

    public function delete(User $user, Post $post): bool
    {
        return $user->id == $post->user_id;
    }

    public function publish(User $user, Post $post): bool
    {
        return $user->id == $post->user_id && $post->status == PostStatusEnum::DRAFT;
    }

}
