<?php

namespace App\Policies;

use App\Models\AdBanner;
use App\Models\User;

class AdBannerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEditor();
    }

    public function update(User $user, AdBanner $adBanner): bool
    {
        return $user->isEditor();
    }
}
