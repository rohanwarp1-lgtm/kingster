<?php

namespace App\Modules\FbaAuto\Policies;

use App\Models\User;
use App\Modules\FbaAuto\Models\FbaAuto;

class FbaAutoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.view');
    }

    public function view(User $user, FbaAuto $fbaAuto): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.view');
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.create');
    }

    public function update(User $user, FbaAuto $fbaAuto): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.update');
    }

    public function delete(User $user, FbaAuto $fbaAuto): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.delete');
    }

    public function export(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('fba-auto.export');
    }
}
