<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function view(User $user)
    {
        return $user->userRole->role->id == 1;
    }

    public function update(User $user)
    {
    }

    public function delete(User $user)
    {
        return $user->userRole->role->id == 1;
    }
}
