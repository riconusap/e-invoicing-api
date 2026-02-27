<?php

namespace App\Policies;

use App\Models\Placement;
use App\Models\User;

class PlacementPolicy
{
    /**
     * Determine if the user can view the placement
     */
    public function view(User $user, Placement $placement): bool
    {
        return $user->id === $placement->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create a placement
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the placement
     */
    public function update(User $user, Placement $placement): bool
    {
        return $user->id === $placement->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the placement
     */
    public function delete(User $user, Placement $placement): bool
    {
        return $user->id === $placement->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the placement
     */
    public function restore(User $user, Placement $placement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the placement
     */
    public function forceDelete(User $user, Placement $placement): bool
    {
        return $user->isAdmin();
    }
}
