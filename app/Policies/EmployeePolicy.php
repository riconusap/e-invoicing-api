<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    /**
     * Determine if the user can view the employee
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->id === $employee->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create an employee
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the employee
     */
    public function update(User $user, Employee $employee): bool
    {
        return $user->id === $employee->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the employee
     */
    public function delete(User $user, Employee $employee): bool
    {
        return $user->id === $employee->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the employee
     */
    public function restore(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the employee
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }
}
