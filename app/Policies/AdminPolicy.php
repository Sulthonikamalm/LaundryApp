<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdminPolicy
{
    use HandlesAuthorization;

    public function before(Admin $admin, $ability)
    {
        if ($admin->isOwner()) {
            return true;
        }
    }

    public function viewAny(Admin $admin)
    {
        return false;
    }

    public function view(Admin $admin, Admin $model)
    {
        return $admin->id === $model->id;
    }

    public function create(Admin $admin)
    {
        return false;
    }

    public function update(Admin $admin, Admin $model)
    {
        return $admin->id === $model->id;
    }

    public function delete(Admin $admin, Admin $model)
    {
        return false;
    }
}
