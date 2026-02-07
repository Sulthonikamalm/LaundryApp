<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * RevenueChartWidget - Grafik Tren Pendapatan
 * 
 * DeepUI: Line chart untuk visualisasi pertumbuhan bisnis.
 * DeepPerformance: Menggunakan database aggregate dan caching.
 * DeepReasoning: Revenue dihitung dari price_at_transaction (snapshot).
 */
class RevenueChartWidget extends LineChartWidget
{
    protected static ?string $heading = 'Tren Pendapatan 30 Hari Terakhir';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $maxHeight = '300px';

    // Filter property
    public ?string $filter = '30';

    protected function getFilters(): ?array
    {
        return [
            '7' => '7 Hari Terakhir',
            '14' => '14 Hari Terakhir',
            '30' => '30 Hari Terakhir',
            '90' => '3 Bulan Terakhir',
        ];
    }

    protected function getData(): array
    {
        $days = (int) $this->filter;
        
        // DeepPerformance: EXTREME CACHING - 1 jam untuk chart data
        // Reasoning: Historical data jarang berubah, prioritas speed over freshness
        $cacheKey = "revenue_chart_{$days}_" . auth()->id();
        $cacheTtl = 3600; // 1 jam

        return Cache::remember($cacheKey, $cacheTtl, function () use ($days) {
            return $this->calculateChartData($days);
        });
    }

    /**
     * Calculate chart data using database aggregation.
     * 
     * DeepDive: Menggunakan GROUP BY date untuk efisiensi.
     * DeepReasoning: Revenue = SUM(price_at_transaction * quantity) dari TransactionDetail.
     * 
     * @param int $days
     * @return array
     */
    protected function calculateChartData(int $days): array
    {
        $startDate = now()->subDays($days)->startOfDay();
        $endDate = now()->endOfDay();

        // Query agregasi per tanggal
        // DeepReasoning: Menggunakan price_at_transaction, bukan base_price saat ini
        $revenueData = TransactionDetail::join('transactions', 'transaction_details.transaction_id', '=', 'transactions.id')
            ->whereBetween('transactions.order_date', [$startDate, $endDate])
            ->where('transactions.status', '!=', 'cancelled')
            ->whereNull('transaction_details.deleted_at')
            ->selectRaw('DATE(transactions.order_date) as date, SUM(transaction_details.subtotal) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('revenue', 'date')
            ->toArray();

        // Transaction count per tanggal
        $txCountData = Transaction::whereBetween('order_date', [$startDate, $endDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('DATE(order_date) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Build labels and datasets
        $labels = [];
        $revenueValues = [];
        $txCountValues = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d M');
            $revenueValues[] = (float) ($revenueData[$date] ?? 0);
            $txCountValues[] = (int) ($txCountData[$date] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pendapatan (Rp)',
                    'data' => $revenueValues,
                    'borderColor' => '#10B981', // Green
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.4,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $txCountValues,
                    'borderColor' => '#6366F1', // Indigo
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'fill' => false,
                    'tension' => 0.4,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'left',
                    'ticks' => [
                        'callback' => "function(value) { return 'Rp ' + value.toLocaleString('id-ID'); }",
                    ],
                ],
                'y1' => [
                    'type' => 'linear',
                    'display' => true,
                    'position' => 'right',
                    'grid' => [
                        'drawOnChartArea' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
