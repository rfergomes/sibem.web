<?php

namespace App\Policies;

use App\Models\Local;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class LocalPolicy
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
    public function view(User $user, Local $local): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin Sistema (1) can create anywhere.
        // Admin Regional (2) can create only in their own Regional (enforced by Controller validation).
        return in_array($user->perfil_id, [1, 2]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Local $local): bool
    {
        if ($user->perfil_id == 1)
            return true;

        if ($user->perfil_id == 2) {
            return $user->regional_id == $local->regional_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Local $local): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Local $local): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Local $local): bool
    {
        return $user->perfil_id == 1;
    }
}
