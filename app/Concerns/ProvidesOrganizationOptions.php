<?php

namespace App\Concerns;

use App\Models\Organization;
use App\Models\User;

trait ProvidesOrganizationOptions
{
    /**
     * @return array<int, string>
     */
    protected function organizationOptions(User $actor): array
    {
        return Organization::query()
            ->forActor($actor)
            ->orderBy('naam')
            ->pluck('naam', 'org_id')
            ->all();
    }
}
