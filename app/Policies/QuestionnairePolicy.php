<?php

namespace App\Policies;

use App\Models\User;

class QuestionnairePolicy
{
    public function manage(User $user): bool
    {
        return $user->isAdmin();
    }
}
