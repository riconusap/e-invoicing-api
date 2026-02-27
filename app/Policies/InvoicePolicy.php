<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    /**
     * Determine if the user can view the invoice
     */
    public function view(User $user, Invoice $invoice): bool
    {
        // Admin can view all invoices, others can only view their own
        return $user->id === $invoice->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can create an invoice
     */
    public function create(User $user): bool
    {
        // Only authenticated users can create invoices
        return true;
    }

    /**
     * Determine if the user can update the invoice
     */
    public function update(User $user, Invoice $invoice): bool
    {
        // Admin can update all invoices, others can only update their own
        return $user->id === $invoice->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can delete the invoice
     */
    public function delete(User $user, Invoice $invoice): bool
    {
        // Admin can delete all invoices, others can only delete their own
        return $user->id === $invoice->created_by || $user->isAdmin();
    }

    /**
     * Determine if the user can restore the invoice
     */
    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can permanently delete the invoice
     */
    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->isAdmin();
    }
}
