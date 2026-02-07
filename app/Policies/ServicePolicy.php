<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Service;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * ServicePolicy - Kebijakan Otorisasi untuk Master Layanan
 * 
 * DeepSecurity: Mengontrol akses ke master data layanan.
 * Deepsecrethacking: Mencegah kasir mengubah harga layanan untuk keuntungan pribadi.
 * 
 * Hak Akses:
 * - Owner: Full access - bisa mengubah harga, menambah/hapus layanan
 * - Kasir: HANYA VIEW - tidak boleh mengubah master data
 * 
 * Alasan: Jika kasir bisa mengubah harga, ada risiko:
 * 1. Kasir menurunkan harga untuk teman/keluarga
 * 2. Kasir menaikkan harga dan mengantongi selisihnya
 * 
 * @package App\Policies
 */
class ServicePolicy
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
        // Deepsecrethacking: User tidak aktif = tidak ada akses
        if (!$admin->is_active) {
            return false;
        }

        // Owner mendapat akses penuh ke master data
        if ($admin->isOwner()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the admin can view any services.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        // Kasir boleh melihat daftar layanan (untuk membuat transaksi)
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can view the service.
     * 
     * @param Admin $admin
     * @param Service $service
     * @return bool
     */
    public function view(Admin $admin, Service $service): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can create services.
     * 
     * DeepSecurity: HANYA OWNER yang boleh menambah layanan baru.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        // Kasir TIDAK BOLEH menambah layanan
        return false;
    }

    /**
     * Determine whether the admin can update the service.
     * 
     * DeepSecurity: HANYA OWNER yang boleh mengubah harga/detail layanan.
     * 
     * Deepsecrethacking: Ini adalah titik kritis!
     * Jika kasir bisa edit harga, mereka bisa:
     * - Menurunkan harga untuk teman (kolusi)
     * - Menaikkan harga dan mengambil selisih (penggelapan)
     * 
     * @param Admin $admin
     * @param Service $service
     * @return bool
     */
    public function update(Admin $admin, Service $service): bool
    {
        // Kasir TIDAK BOLEH mengubah master data layanan
        return false;
    }

    /**
     * Determine whether the admin can delete the service.
     * 
     * @param Admin $admin
     * @param Service $service
     * @return bool
     */
    public function delete(Admin $admin, Service $service): bool
    {
        return false; // Owner only (handled in before())
    }

    /**
     * Determine whether the admin can restore the service.
     * 
     * @param Admin $admin
     * @param Service $service
     * @return bool
     */
    public function restore(Admin $admin, Service $service): bool
    {
        return false; // Owner only
    }

    /**
     * Determine whether the admin can permanently delete the service.
     * 
     * @param Admin $admin
     * @param Service $service
     * @return bool
     */
    public function forceDelete(Admin $admin, Service $service): bool
    {
        // Deepsecrethacking: Jangan pernah force delete master data
        // Ini bisa merusak referensi di transaction_details
        return false;
    }
}
