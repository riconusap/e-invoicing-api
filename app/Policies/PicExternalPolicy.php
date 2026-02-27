<?php

namespace App\Policies;

use App\Models\PicExternal;
use App\Models\User;

class PicExternalPolicy
{
    /**
     * Determine if the user can view the pic external
     */
    public function view(User $user, PicExternal $pic): bool
    {
        return $user->id === $pic->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create a pic external
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the pic external
     */
    public function update(User $user, PicExternal $pic): bool
    {
        return $user->id === $pic->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the pic external
     */
    public function delete(User $user, PicExternal $pic): bool
    {
        return $user->id === $pic->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the pic external
     */
    public function restore(User $user, PicExternal $pic): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the pic external
     */
    public function forceDelete(User $user, PicExternal $pic): bool
    {
        return $user->isAdmin();
    }
}
