<?php

namespace App\Policies;

use App\Models\Admin;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * AdminPolicy - Authorization untuk Admin Resource
 * 
 * DeepSecurity: Owner (role=owner) memiliki akses penuh.
 * DeepSecurity: Admin biasa hanya bisa view/update profil sendiri.
 * DeepReasoning: Mencegah admin biasa mengedit data admin lain.
 */
class AdminPolicy
{
    use HandlesAuthorization;

    /**
     * DeepSecurity: Owner bypass semua authorization check.
     * 
     * @param Admin $admin
     * @param string $ability
     * @return bool|null
     */
    public function before(Admin $admin, $ability)
    {
        if ($admin->isOwner()) {
            return true;
        }
    }

    /**
     * DeepSecurity: Hanya owner yang bisa melihat daftar admin.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin)
    {
        return $admin->isOwner();
    }

    /**
     * DeepSecurity: Admin bisa view profil sendiri, owner bisa view semua.
     * 
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function view(Admin $admin, Admin $model)
    {
        return $admin->id === $model->id || $admin->isOwner();
    }

    /**
     * DeepSecurity: Hanya owner yang bisa create admin baru.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin)
    {
        return $admin->isOwner();
    }

    /**
     * DeepSecurity: Admin bisa update profil sendiri, owner bisa update semua.
     * 
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function update(Admin $admin, Admin $model)
    {
        return $admin->id === $model->id || $admin->isOwner();
    }

    /**
     * DeepSecurity: Hanya owner yang bisa delete admin.
     * DeepReasoning: Mencegah admin menghapus diri sendiri atau admin lain.
     * 
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function delete(Admin $admin, Admin $model)
    {
        // Owner bisa delete, tapi tidak bisa delete diri sendiri
        return $admin->isOwner() && $admin->id !== $model->id;
    }

    /**
     * DeepSecurity: Hanya owner yang bisa restore admin yang di-soft delete.
     * 
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function restore(Admin $admin, Admin $model)
    {
        return $admin->isOwner();
    }

    /**
     * DeepSecurity: Hanya owner yang bisa force delete (permanent delete).
     * 
     * @param Admin $admin
     * @param Admin $model
     * @return bool
     */
    public function forceDelete(Admin $admin, Admin $model)
    {
        return $admin->isOwner();
    }
}
