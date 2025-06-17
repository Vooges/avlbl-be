<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserItemSize;

class UserItemSizePolicy
{

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }


    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserItemSize $userItemSize): bool
    {
        return $user->isAdmin() || $userItemSize->user_id === $user->id;
    }
}
