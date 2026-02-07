<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Admin;
use App\Models\Payment;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * PaymentPolicy - Kebijakan Otorisasi untuk Pembayaran
 * 
 * DeepSecurity: Policy paling kritis karena berhubungan langsung dengan uang.
 * Deepsecrethacking: Mencegah semua bentuk manipulasi finansial.
 * 
 * Hak Akses:
 * - Owner: Full access termasuk void/refund
 * - Kasir: Create (terima pembayaran), View saja
 *          TIDAK BOLEH: Edit, Delete, Refund
 * 
 * Risiko yang dicegah:
 * 1. Kasir menghapus bukti pembayaran tunai
 * 2. Kasir mengubah jumlah pembayaran setelah dicatat
 * 3. Kasir melakukan refund fiktif
 * 
 * @package App\Policies
 */
class PaymentPolicy
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
     * Determine whether the admin can view any payments.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can view the payment.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function view(Admin $admin, Payment $payment): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can create payments.
     * 
     * DeepSecurity: Kasir BOLEH mencatat pembayaran baru.
     * Catatan: Setiap pembayaran otomatis di-log dengan processed_by.
     * 
     * @param Admin $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->isKasir();
    }

    /**
     * Determine whether the admin can update the payment.
     * 
     * DeepSecurity: KASIR TIDAK BOLEH MENGEDIT PEMBAYARAN.
     * 
     * Deepsecrethacking: Ini adalah celah keamanan klassik!
     * Jika kasir bisa edit:
     * - Terima tunai 100rb, catat 50rb, kantongi 50rb
     * - Ubah payment_method dari 'cash' ke 'transfer' untuk tutup jejak
     * 
     * Solusi: Pembayaran yang sudah dicatat = final.
     * Jika ada kesalahan, Owner harus melakukan koreksi.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function update(Admin $admin, Payment $payment): bool
    {
        // Kasir TIDAK BOLEH mengedit pembayaran yang sudah dicatat
        return false;
    }

    /**
     * Determine whether the admin can delete the payment.
     * 
     * DeepSecurity: ABSOLUTE NO untuk kasir.
     * 
     * Deepsecrethacking: Delete payment = hilang bukti = penggelapan sempurna.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function delete(Admin $admin, Payment $payment): bool
    {
        return false; // Owner only
    }

    /**
     * Determine whether the admin can restore the payment.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function restore(Admin $admin, Payment $payment): bool
    {
        return false; // Owner only
    }

    /**
     * Determine whether the admin can permanently delete the payment.
     * 
     * DeepSecurity: BAHKAN OWNER tidak boleh force delete payment.
     * Data finansial harus ada audit trail selamanya.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function forceDelete(Admin $admin, Payment $payment): bool
    {
        // Deepsecrethacking: TIDAK ADA yang boleh menghapus permanen
        // Bahkan Owner. Ini untuk keperluan audit.
        return false;
    }

    /**
     * Determine whether the admin can refund the payment.
     * 
     * Custom ability untuk proses refund.
     * 
     * DeepSecurity: HANYA OWNER yang boleh melakukan refund.
     * 
     * @param Admin $admin
     * @param Payment $payment
     * @return bool
     */
    public function refund(Admin $admin, Payment $payment): bool
    {
        // Deepsecrethacking: Refund fiktif adalah salah satu
        // cara penggelapan paling umum di retail.
        return false; // Owner only (handled in before())
    }
}
