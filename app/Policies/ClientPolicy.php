<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    /**
     * Determine if the user can view the client
     */
    public function view(User $user, Client $client): bool
    {
        return $user->id === $client->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create a client
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the client
     */
    public function update(User $user, Client $client): bool
    {
        return $user->id === $client->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the client
     */
    public function delete(User $user, Client $client): bool
    {
        return $user->id === $client->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the client
     */
    public function restore(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the client
     */
    public function forceDelete(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }
}
