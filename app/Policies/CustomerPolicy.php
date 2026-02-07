<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * CustomerPolicy - Kebijakan Otorisasi untuk Data Pelanggan
 * 
 * DeepSecurity: Melindungi data pribadi pelanggan (GDPR/UU PDP compliance).
 * Deepsecrethacking: Mencegah pencurian database pelanggan.
 * 
 * Hak Akses:
 * - Owner: Full access
 * - Kasir: Create (registrasi pelanggan baru), View, Edit (update alamat)
 *          TIDAK BOLEH: Delete (untuk audit trail)
 * 
 * @package App\Policies
 */
class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * 
     * @param Admin $admin
     * @param string $ability
     * @return bool|null
     */
    public function before(Admin $admin, string $ability): ?bool
    {
        if (!$admin->is_active) {
            return false;
        }

        if ($admin->isOwner()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the admin can view any customers.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can view the customer.
     * 
     * @param Admin $admin
     * @param Customer $customer
     * @return bool
     */
    public function view(Admin $admin, Customer $customer): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can create customers.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        // Kasir boleh mendaftarkan pelanggan baru
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can update the customer.
     * 
     * @param Admin $admin
     * @param Customer $customer
     * @return bool
     */
    public function update(Admin $admin, Customer $customer): bool
    {
        // Kasir boleh update data pelanggan (alamat, telepon)
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can delete the customer.
     * 
     * DeepSecurity: Delete customer = kehilangan history transaksi.
     * 
     * @param Admin $admin
     * @param Customer $customer
     * @return bool
     */
    public function delete(Admin $admin, Customer $customer): bool
    {
        return false; // Owner only
    }

    /**
     * Determine whether the admin can restore the customer.
     * 
     * @param Admin $admin
     * @param Customer $customer
     * @return bool
     */
    public function restore(Admin $admin, Customer $customer): bool
    {
        return false; // Owner only
    }

    /**
     * Determine whether the admin can permanently delete.
     * 
     * @param Admin $admin
     * @param Customer $customer
     * @return bool
     */
    public function forceDelete(Admin $admin, Customer $customer): bool
    {
        return false; // Never - audit trail
    }
}
