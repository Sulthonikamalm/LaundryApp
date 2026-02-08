<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\CheckOverdueTransactionsJob;
use App\Jobs\CleanupExpiredPaymentsJob;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     * 
     * DeepAdvanced: Scheduler sebagai trigger, logika bisnis di Job Classes.
     * DeepPerformance: withoutOverlapping() mencegah race condition.
     */
    protected function schedule(Schedule $schedule): void
    {
        // 1. Cek transaksi overdue setiap jam
        // DeepState: Untuk notifikasi admin/customer tentang cucian terlambat
        $schedule->job(new CheckOverdueTransactionsJob())
            ->hourly()
            ->withoutOverlapping();

        // 2. Cleanup expired payments setiap hari jam 2 pagi
        // DeepState: Membersihkan payment pending yang abandoned
        $schedule->job(new CleanupExpiredPaymentsJob())
            ->dailyAt('02:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
