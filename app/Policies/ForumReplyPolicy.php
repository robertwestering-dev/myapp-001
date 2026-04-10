<?php

namespace App\Policies;

use App\Models\ForumReply;
use App\Models\User;

class ForumReplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user !== null;
    }

    public function view(User $user, ForumReply $forumReply): bool
    {
        return $user !== null;
    }

    public function create(User $user): bool
    {
        return $user !== null;
    }

    public function update(User $user, ForumReply $forumReply): bool
    {
        return $user->isAdmin() || $forumReply->user_id === $user->getKey();
    }

    public function delete(User $user, ForumReply $forumReply): bool
    {
        return $user->isAdmin() || $forumReply->user_id === $user->getKey();
    }
}
