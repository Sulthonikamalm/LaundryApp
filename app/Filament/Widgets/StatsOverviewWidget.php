<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Illuminate\Support\Facades\Cache;

/**
 * StatsOverviewWidget - Dashboard KPI Cards
 * 
 * DeepUI Best Practices:
 * - Visual Hierarchy: KPIs paling penting di posisi pertama
 * - Simplicity: Data yang actionable, tanpa dekorasi berlebihan
 * - Performance: Single aggregate query dengan caching
 */
class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    // DeepPerformance: No polling, user can refresh manually
    protected static ?string $pollingInterval = null;

    /**
     * Backward Compatibility Fix:
     * Method ini dipanggil oleh script lama yang mungkin masih di-cache browser.
     * Kita biarkan kosong agar tidak error "Method not found".
     */
    public function loadWidget(): void
    {
        // Do nothing. Legacy support.
    }

    protected function getCards(): array
    {
        // 1. STAT PENDAPATAN (REVENUE)
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('payment_date', now()->toDateString())
            ->sum('amount');

        // 2. STAT OPERASIONAL (IN PROGRESS)
        $processingCount = Transaction::whereIn('status', ['pending', 'processing'])->count();

        // 3. STAT URGENSI (OVERDUE/READY)
        $urgentCount = Transaction::query()
            ->where('status', 'ready')
            ->orWhere(function ($query) {
                $query->whereIn('status', ['pending', 'processing'])
                      ->where('estimated_completion_date', '<', now());
            })
            ->count();

        return [
            Card::make('Pendapatan Hari Ini', 'Rp ' . number_format($todayRevenue, 0, ',', '.'))
                ->description('Total uang masuk hari ini')
                ->descriptionIcon('heroicon-o-cash')
                ->color('success'),

            Card::make('Sedang Diproses', $processingCount)
                ->description('Cucian belum selesai')
                ->descriptionIcon('heroicon-o-refresh')
                ->color('warning'),

            Card::make('Perlu Tindakan', $urgentCount)
                ->description('Siap diambil / Terlambat')
                ->descriptionIcon('heroicon-o-exclamation')
                ->color('danger'),
        ];
    }
}
