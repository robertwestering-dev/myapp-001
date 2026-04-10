<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, ForumThread $forumThread): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ForumThread $forumThread): bool
    {
        return $user->isAdmin() || $forumThread->user_id === $user->getKey();
    }

    public function delete(User $user, ForumThread $forumThread): bool
    {
        return $user->isAdmin() || $forumThread->user_id === $user->getKey();
    }
}
