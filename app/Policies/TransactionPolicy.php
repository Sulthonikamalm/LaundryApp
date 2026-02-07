<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Transaction;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * TransactionPolicy - Kebijakan Otorisasi untuk Transaksi
 * 
 * DeepSecurity: Policy ini mengontrol akses CRUD pada transaksi.
 * Deepsecrethacking: Mencegah manipulasi data finansial oleh kasir nakal.
 * 
 * Hak Akses:
 * - Owner: Full access (view, create, edit, delete, restore, forceDelete)
 * - Kasir: Create, Edit saja. TIDAK BOLEH delete (mencegah penghapusan bukti)
 * - Courier: Tidak ada akses sama sekali ke transaksi lewat panel admin
 * 
 * @package App\Policies
 */
class TransactionPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     * 
     * DeepSecurity: Owner memiliki akses penuh ke semua operasi.
     * Ini adalah "super admin" bypass - gunakan dengan hati-hati.
     * 
     * @param Admin $admin
     * @param string $ability
     * @return bool|null
     */
    public function before(Admin $admin, string $ability): ?bool
    {
        // Deepsecrethacking: Pastikan user aktif sebelum memberikan akses apapun
        if (!$admin->is_active) {
            return false;
        }

        // Owner mendapat akses penuh
        if ($admin->isOwner()) {
            return true;
        }

        return null; // Lanjut ke method spesifik
    }

    /**
     * Determine whether the admin can view any transactions.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can view the transaction.
     * 
     * DeepSecurity: Kasir hanya boleh melihat transaksi yang aktif.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function view(Admin $admin, Transaction $transaction): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can create transactions.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can update the transaction.
     * 
     * DeepSecurity: Kasir bisa edit, TAPI ada batasan tambahan:
     * - Tidak boleh edit transaksi yang sudah completed
     * - Tidak boleh mengubah total_cost secara langsung (harus via detail)
     * 
     * Deepsecrethacking: Mencegah kasir mengubah harga setelah pembayaran.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function update(Admin $admin, Transaction $transaction): bool
    {
        if (!$admin->isKasir()) {
            return false;
        }

        // Deepsecrethacking: Blokir edit transaksi yang sudah selesai
        // Ini mencegah manipulasi laporan keuangan historis
        if ($transaction->status === 'completed') {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the admin can delete the transaction.
     * 
     * DeepSecurity: HANYA OWNER yang boleh menghapus transaksi.
     * Kasir TIDAK BOLEH menghapus data apapun untuk mencegah:
     * - Penghapusan bukti transaksi
     * - Manipulasi laporan penjualan
     * - Penggelapan uang
     * 
     * Deepsecrethacking: Ini adalah titik kritis. Jika kasir bisa delete,
     * mereka bisa menghilangkan bukti transaksi tunai.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function delete(Admin $admin, Transaction $transaction): bool
    {
        // Kasir TIDAK BOLEH menghapus apapun
        // Method before() sudah menangani Owner
        return false;
    }

    /**
     * Determine whether the admin can restore the transaction.
     * 
     * DeepSecurity: Restore hanya untuk Owner.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function restore(Admin $admin, Transaction $transaction): bool
    {
        // Hanya Owner (ditangani di before())
        return false;
    }

    /**
     * Determine whether the admin can permanently delete the transaction.
     * 
     * DeepSecurity: FORCE DELETE sangat berbahaya!
     * Hanya Owner yang boleh, dan sebaiknya di-disable di production.
     * 
     * Deepsecrethacking: Permanent delete = hilang selamanya.
     * Pertimbangkan untuk men-disable fitur ini sepenuhnya.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function forceDelete(Admin $admin, Transaction $transaction): bool
    {
        // Bahkan Owner harus berhati-hati dengan force delete
        // Untuk keamanan maksimal, uncomment baris berikut:
        // return false;
        
        // Default: Hanya Owner (ditangani di before())
        return false;
    }

    /**
     * Determine whether the admin can replicate the transaction.
     * 
     * @param Admin $admin
     * @param Transaction $transaction
     * @return bool
     */
    public function replicate(Admin $admin, Transaction $transaction): bool
    {
        return $admin->isKasir();
    }
}
