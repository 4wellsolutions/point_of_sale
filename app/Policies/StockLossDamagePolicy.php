<?php

namespace App\Policies;

use App\Models\StockLossDamage;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockLossDamagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any stock loss/damage records.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the stock loss/damage record.
     */
    public function view(User $user, StockLossDamage $stockLossDamage)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create stock loss/damage records.
     */
    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update the stock loss/damage record.
     */
    public function update(User $user, StockLossDamage $stockLossDamage)
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the stock loss/damage record.
     */
    public function delete(User $user, StockLossDamage $stockLossDamage)
    {
        return $user->hasRole('admin');
    }
}
