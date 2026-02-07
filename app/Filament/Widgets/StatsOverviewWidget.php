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
 * DeepUI: Kartu statistik reaktif untuk Owner.
 * DeepPerformance: Menggunakan aggregate query dan EXTREME caching.
 * DeepReasoning: Semua angka finansial dihitung dari price_at_transaction.
 * DeepTeknik: Lazy loading dengan defer untuk instant page load.
 */
class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    // DeepPerformance: DISABLE polling untuk mengurangi roundtrip ke TiDB Frankfurt
    // User bisa manual refresh jika perlu data terbaru
    protected static ?string $pollingInterval = null;

    // DeepPerformance: Lazy load widget untuk instant page render
    public bool $readyToLoad = false;

    public function mount(): void
    {
        // Defer loading sampai user scroll atau idle
        $this->readyToLoad = false;
    }

    public function loadWidget(): void
    {
        $this->readyToLoad = true;
    }

    protected function getCards(): array
    {
        // DeepPerformance: Return empty state jika belum ready
        if (!$this->readyToLoad) {
            return [
                Card::make('Loading...', '⏳')
                    ->description('Memuat data...')
                    ->color('secondary'),
            ];
        }

        // DeepPerformance: EXTREME CACHING - 30 menit untuk dashboard stats
        // Reasoning: Stats tidak perlu real-time, user bisa refresh manual
        $cacheKey = 'dashboard_stats_' . auth()->id();
        $cacheTtl = 1800; // 30 menit

        $stats = Cache::remember($cacheKey, $cacheTtl, function () {
            return $this->calculateStats();
        });

        return [
            // 1. TOTAL OUTSTANDING (Piutang)
            Card::make('Total Piutang', 'Rp ' . number_format($stats['outstanding'], 0, ',', '.'))
                ->description('Tagihan belum lunas')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color($stats['outstanding'] > 0 ? 'danger' : 'success')
                ->chart($stats['outstanding_trend'])
                ->extraAttributes([
                    'title' => 'Cache: 30 menit. Klik refresh untuk update.',
                ]),

            // 2. PENDAPATAN HARI INI
            Card::make('Pendapatan Hari Ini', 'Rp ' . number_format($stats['today_revenue'], 0, ',', '.'))
                ->description($stats['today_tx_count'] . ' transaksi')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart($stats['revenue_trend'])
                ->extraAttributes([
                    'title' => 'Cache: 30 menit. Klik refresh untuk update.',
                ]),

            // 3. TRANSAKSI PENDING
            Card::make('Cucian Pending', $stats['pending_count'])
                ->description('Menunggu proses')
                ->descriptionIcon('heroicon-o-clock')
                ->color($stats['pending_count'] > 10 ? 'warning' : 'primary')
                ->extraAttributes([
                    'title' => 'Cache: 30 menit. Klik refresh untuk update.',
                ]),

            // 4. SIAP DIAMBIL
            Card::make('Siap Diambil', $stats['ready_count'])
                ->description('Menunggu pelanggan')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success')
                ->extraAttributes([
                    'title' => 'Cache: 30 menit. Klik refresh untuk update.',
                ]),
        ];
    }

    /**
     * Refresh cache manually.
     * 
     * DeepUI: Allow user to force refresh data.
     * 
     * @return void
     */
    public function refreshStats(): void
    {
        $cacheKey = 'dashboard_stats_' . auth()->id();
        Cache::forget($cacheKey);
        
        $this->emit('refreshWidget');
        
        $this->notify('success', 'Data berhasil di-refresh!');
    }

    /**
     * Calculate all dashboard statistics.
     * 
     * DeepDive: Semua kalkulasi menggunakan database aggregate.
     * DeepReasoning: Revenue dihitung dari payment completed, bukan total_cost.
     * 
     * @return array
     */
    protected function calculateStats(): array
    {
        $today = now()->toDateString();

        // 1. Outstanding (Total Tagihan Belum Lunas)
        // DeepReasoning: total_cost - total_paid untuk semua transaksi non-cancelled
        $outstanding = Transaction::whereIn('payment_status', ['unpaid', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COALESCE(SUM(total_cost - total_paid), 0) as outstanding')
            ->value('outstanding') ?? 0;

        // 2. Pendapatan Hari Ini (dari Payment completed)
        // DeepReasoning: Pendapatan riil = uang yang sudah masuk (payments)
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('payment_date', $today)
            ->sum('amount') ?? 0;

        // 3. Jumlah Transaksi Hari Ini
        $todayTxCount = Transaction::whereDate('order_date', $today)->count();

        // 4. Status Counts (DeepPerformance: Single query with grouping)
        $statusCounts = Transaction::whereIn('status', ['pending', 'processing', 'ready'])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 5. Trend Data (Last 7 days for sparkline)
        $outstandingTrend = $this->getOutstandingTrend();
        $revenueTrend = $this->getRevenueTrend();

        return [
            'outstanding' => (float) $outstanding,
            'today_revenue' => (float) $todayRevenue,
            'today_tx_count' => $todayTxCount,
            'pending_count' => $statusCounts['pending'] ?? 0,
            'processing_count' => $statusCounts['processing'] ?? 0,
            'ready_count' => $statusCounts['ready'] ?? 0,
            'outstanding_trend' => $outstandingTrend,
            'revenue_trend' => $revenueTrend,
        ];
    }

    /**
     * Get outstanding trend for last 7 days.
     * 
     * @return array
     */
    /**
     * Get outstanding trend (New Debt Created) for last 7 days.
     * 
     * DeepPerformance: Optimized to single Aggregate Query.
     * Logic changed to "New Outstanding Created" to avoid heavy historical sum.
     */
    protected function getOutstandingTrend(): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        
        $data = Transaction::where('created_at', '>=', $startDate)
            ->selectRaw('DATE(created_at) as date, SUM(total_cost - total_paid) as outstanding')
            ->groupBy('date')
            ->pluck('outstanding', 'date')
            ->toArray();

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $value = $data[$date] ?? 0; // O(1) Lookup
            $trend[] = (int) ($value / 1000);
        }
        
        return $trend;
    }

    /**
     * Get revenue trend for last 7 days.
     * 
     * DeepPerformance: Optimized to single Aggregate Query.
     */
    protected function getRevenueTrend(): array
    {
        $startDate = now()->subDays(6)->startOfDay();

        $data = Payment::where('status', 'completed')
            ->where('payment_date', '>=', $startDate)
            ->selectRaw('DATE(payment_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total', 'date')
            ->toArray();

        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $value = $data[$date] ?? 0; // O(1) Lookup
            $trend[] = (int) ($value / 1000);
        }
        
        return $trend;
    }
}
