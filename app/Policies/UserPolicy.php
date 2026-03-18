<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only Admin System (1) and Admin Regional (2) can view the full list
        return $user->perfil_id <= 2;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->perfil_id <= 3;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $currentUser, User $targetUser): bool
    {
        // 1. Admin System can edit everyone
        if ($currentUser->perfil_id == 1)
            return true;

        // 2. Admin Regional
        if ($currentUser->perfil_id == 2) {
            // Cannot edit Admin System (1)
            if ($targetUser->perfil_id == 1)
                return false;
            // Can edit own regional users
            return $currentUser->regional_id == $targetUser->regional_id;
        }

        // 3. Admin Local
        if ($currentUser->perfil_id == 3) {
            // Cannot edit superiors (1 or 2) or other Admin Locals (3) ? 
            // Usually Admin Local manages Operators (4)
            if ($targetUser->perfil_id <= 3)
                return false;

            // Can edit if user belongs to one of the admin's locales
            // Logic: Does the target user belong to a local that this admin manages?
            $adminLocals = $currentUser->locais->pluck('id')->toArray();
            $targetLocals = $targetUser->locais->pluck('id')->toArray();

            // If intersection is not empty
            return !empty(array_intersect($adminLocals, $targetLocals));
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $currentUser, User $targetUser): bool
    {
        return $this->update($currentUser, $targetUser); // Same logic
    }
}
