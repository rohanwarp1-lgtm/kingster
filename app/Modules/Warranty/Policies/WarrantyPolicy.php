<?php

namespace App\Modules\Warranty\Policies;

use App\Models\User;
use App\Modules\Warranty\Models\WarrantyRegistration;

class WarrantyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.view');
    }

    public function view(User $user, WarrantyRegistration $warranty): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.view');
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.create');
    }

    public function approve(User $user, WarrantyRegistration $warranty): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.approve');
    }

    public function reject(User $user, WarrantyRegistration $warranty): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.reject');
    }

    public function delete(User $user, WarrantyRegistration $warranty): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('warranty.delete');
    }
}
