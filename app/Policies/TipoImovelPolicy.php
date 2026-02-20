<?php

namespace App\Policies;

use App\Models\TipoImovel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TipoImovelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admin Sistema can view all
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, TipoImovel $tipoImovel): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admin Sistema can create
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, TipoImovel $tipoImovel): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, TipoImovel $tipoImovel): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TipoImovel $tipoImovel): bool
    {
        return $user->perfil_id == 1;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TipoImovel $tipoImovel): bool
    {
        return $user->perfil_id == 1;
    }
}
