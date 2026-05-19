<?php

namespace App\Modules\Rma\Policies;

use App\Models\User;
use App\Modules\Rma\Models\RmaTicket;

class RmaPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.view');
    }

    public function view(User $user, RmaTicket $ticket): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.view');
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.create');
    }

    public function update(User $user, RmaTicket $ticket): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.update');
    }

    public function assign(User $user, RmaTicket $ticket): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.assign');
    }

    public function delete(User $user, RmaTicket $ticket): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('rma.delete');
    }
}
