<?php

namespace App\Policies;

use App\Models\Setor;
use App\Models\User;

class SetorPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Setor $setor): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin Sistema (1), Regional (2), Local (3)
        return $user->perfil_id <= 3;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Setor $setor): bool
    {
        return $user->perfil_id <= 3;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Setor $setor): bool
    {
        // Only Admin Sistema (1) and Regional (2)
        return $user->perfil_id <= 2;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Setor $setor): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Setor $setor): bool
    {
        return $user->perfil_id == 1;
    }
}
