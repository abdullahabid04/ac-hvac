<?php

namespace App\Policies;

use App\Models\AcUnit;
use App\Models\User;

class AcUnitPolicy
{
    public function view(User $user, AcUnit $acUnit): bool
    {
        return $user->belongsToOrganization($acUnit->organization_id);
    }

    public function update(User $user, AcUnit $acUnit): bool
    {
        return $user->belongsToOrganization($acUnit->organization_id);
    }

    public function control(User $user, AcUnit $acUnit): bool
    {
        return $user->belongsToOrganization($acUnit->organization_id);
    }

    public function delete(User $user, AcUnit $acUnit): bool
    {
        return false;
    }
}
