<?php

namespace App\Policies;

use App\Models\Igreja;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IgrejaPolicy
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
    public function view(User $user, Igreja $igreja): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return in_array($user->perfil_id, [1, 2, 3]);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Igreja $igreja): bool
    {
        if ($user->perfil_id == 1)
            return true;

        if ($user->perfil_id == 2) {
            return $user->regional_id == $igreja->local->regional_id;
        }

        if ($user->perfil_id == 3) {
            return $user->authorized_locais->contains($igreja->local_id);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Igreja $igreja): bool
    {
        return $this->update($user, $igreja); // Same rules as update
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Igreja $igreja): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Igreja $igreja): bool
    {
        return $user->perfil_id == 1;
    }
}
