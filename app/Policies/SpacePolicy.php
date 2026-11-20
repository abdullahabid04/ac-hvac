<?php

namespace App\Policies;

use App\Models\Space;
use App\Models\User;

class SpacePolicy
{
    public function view(User $user, Space $space): bool
    {
        return $user->belongsToOrganization($space->organization_id);
    }

    public function update(User $user, Space $space): bool
    {
        return $user->belongsToOrganization($space->organization_id);
    }
}
