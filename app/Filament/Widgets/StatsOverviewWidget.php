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
        // DeepPerformance: Cache 5 menit
        $stats = Cache::remember('dashboard_kpi_' . auth()->id(), 300, function () {
            return $this->calculateStats();
        });

        return [
            // 1. PIUTANG AKTIF - Prioritas tinggi, warna merah jika ada
            Card::make('Total Piutang', 'Rp ' . number_format($stats['outstanding'], 0, ',', '.'))
                ->description('Tagihan belum lunas')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color($stats['outstanding'] > 0 ? 'danger' : 'success')
                ->chart($stats['outstanding_trend']),

            // 2. TRANSAKSI AKTIF - Jumlah cucian yang sedang diproses
            Card::make('Transaksi Aktif', $stats['pending_count'] + $stats['processing_count'])
                ->description($stats['pending_count'] . ' pending, ' . $stats['processing_count'] . ' proses')
                ->descriptionIcon('heroicon-o-lightning-bolt')
                ->color('primary'),

            // 3. SIAP DIAMBIL - Customer action needed
            Card::make('Siap Diambil', $stats['ready_count'])
                ->description('Menunggu pelanggan')
                ->descriptionIcon('heroicon-o-check-circle')
                ->color('success'),

            // 4. PENDAPATAN HARI INI
            Card::make('Pendapatan Hari Ini', 'Rp ' . number_format($stats['today_revenue'], 0, ',', '.'))
                ->description($stats['today_tx_count'] . ' transaksi')
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('success')
                ->chart($stats['revenue_trend']),
        ];
    }

    protected function calculateStats(): array
    {
        $today = now()->toDateString();

        // 1. Outstanding - Tagihan belum lunas
        $outstanding = Transaction::whereIn('payment_status', ['unpaid', 'partial'])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('COALESCE(SUM(total_cost - total_paid), 0) as outstanding')
            ->value('outstanding') ?? 0;

        // 2. Pendapatan hari ini
        $todayRevenue = Payment::where('status', 'completed')
            ->whereDate('payment_date', $today)
            ->sum('amount') ?? 0;

        // 3. Jumlah transaksi hari ini
        $todayTxCount = Transaction::whereDate('order_date', $today)->count();

        // 4. Status counts
        $statusCounts = Transaction::whereIn('status', ['pending', 'processing', 'ready'])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        // 5. Trends (7 hari terakhir)
        $outstandingTrend = $this->getTrend('outstanding');
        $revenueTrend = $this->getTrend('revenue');

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

    protected function getTrend(string $type): array
    {
        $startDate = now()->subDays(6)->startOfDay();
        $trend = [];

        if ($type === 'outstanding') {
            $data = Transaction::where('created_at', '>=', $startDate)
                ->selectRaw('DATE(created_at) as date, SUM(total_cost - total_paid) as value')
                ->groupBy('date')
                ->pluck('value', 'date')
                ->toArray();
        } else {
            $data = Payment::where('status', 'completed')
                ->where('payment_date', '>=', $startDate)
                ->selectRaw('DATE(payment_date) as date, SUM(amount) as value')
                ->groupBy('date')
                ->pluck('value', 'date')
                ->toArray();
        }

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trend[] = (int) (($data[$date] ?? 0) / 1000);
        }

        return $trend;
    }
}
