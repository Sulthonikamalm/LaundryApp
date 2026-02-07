<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Transaction;
use App\Policies\CustomerPolicy;
use App\Policies\PaymentPolicy;
use App\Policies\ServicePolicy;
use App\Policies\TransactionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

/**
 * AuthServiceProvider - Registrasi Policy dan Gate
 * 
 * DeepSecurity: Central point untuk semua otorisasi.
 * DeepScale: Mudah ditambah policy baru seiring pertumbuhan aplikasi.
 * 
 * @package App\Providers
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     * 
     * DeepCode: Mapping eksplisit untuk kejelasan dan keamanan.
     * Jangan andalkan auto-discovery di production.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Transaction::class => TransactionPolicy::class,
        Service::class => ServicePolicy::class,
        Payment::class => PaymentPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // ========================================
        // GLOBAL GATES (DeepSecurity)
        // ========================================
        
        /**
         * Gate: access-admin-panel
         * 
         * Deepsecrethacking: Double-check akses panel admin.
         * Meskipun sudah ada canAccessFilament(), gate ini
         * sebagai layer keamanan tambahan.
         */
        Gate::define('access-admin-panel', function ($admin) {
            return $admin->is_active && in_array($admin->role, ['owner', 'kasir']);
        });

        /**
         * Gate: view-financial-reports
         * 
         * DeepSecurity: Laporan keuangan hanya untuk Owner.
         */
        Gate::define('view-financial-reports', function ($admin) {
            return $admin->isOwner();
        });

        /**
         * Gate: manage-admin-users
         * 
         * DeepSecurity: Mengelola user admin hanya untuk Owner.
         * Mencegah kasir membuat akun backdoor.
         */
        Gate::define('manage-admin-users', function ($admin) {
            return $admin->isOwner();
        });

        /**
         * Gate: export-data
         * 
         * Deepsecrethacking: Export data adalah risiko pencurian DB.
         * Hanya Owner yang boleh export.
         */
        Gate::define('export-data', function ($admin) {
            return $admin->isOwner();
        });

        /**
         * Gate: view-audit-logs
         * 
         * DeepSecurity: Audit log hanya untuk Owner.
         */
        Gate::define('view-audit-logs', function ($admin) {
            return $admin->isOwner();
        });
    }
}
