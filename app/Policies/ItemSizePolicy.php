<?php

namespace App\Policies;

use App\Models\ItemSize;
use App\Models\User;

class ItemSizePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ItemSize $itemSize): bool
    {
        return $user->isAdmin();
    }
}
