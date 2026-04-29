<?php

namespace App\Policies;

use App\Models\Advertorial;
use App\Models\User;

class AdvertorialPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEditor();
    }

    public function view(User $user, Advertorial $advertorial): bool
    {
        return $user->isEditor();
    }

    public function create(User $user): bool
    {
        return $user->isEditor();
    }

    public function update(User $user, Advertorial $advertorial): bool
    {
        return $user->isEditor();
    }

    public function delete(User $user, Advertorial $advertorial): bool
    {
        return $user->isEditor();
    }
}
