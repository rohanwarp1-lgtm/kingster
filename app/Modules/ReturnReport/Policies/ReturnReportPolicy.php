<?php

namespace App\Modules\ReturnReport\Policies;

use App\Models\User;
use App\Modules\ReturnReport\Models\ReturnReport;

class ReturnReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('return-report.view');
    }

    public function view(User $user, ReturnReport $report): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('return-report.view');
    }

    public function create(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('return-report.create');
    }

    public function export(User $user): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('return-report.export');
    }

    public function delete(User $user, ReturnReport $report): bool
    {
        return $user->role === 'admin' || $user->hasPermissionTo('return-report.delete');
    }
}
