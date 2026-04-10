<?php

namespace App\Policies;

use App\Models\User;

class BlogPostPolicy
{
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
