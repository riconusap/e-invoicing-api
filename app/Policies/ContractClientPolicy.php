<?php

namespace App\Policies;

use App\Models\ContractClient;
use App\Models\User;

class ContractClientPolicy
{
    /**
     * Determine if the user can view the contract
     */
    public function view(User $user, ContractClient $contract): bool
    {
        return $user->id === $contract->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create a contract
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the contract
     */
    public function update(User $user, ContractClient $contract): bool
    {
        return $user->id === $contract->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the contract
     */
    public function delete(User $user, ContractClient $contract): bool
    {
        return $user->id === $contract->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the contract
     */
    public function restore(User $user, ContractClient $contract): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the contract
     */
    public function forceDelete(User $user, ContractClient $contract): bool
    {
        return $user->isAdmin();
    }
}
