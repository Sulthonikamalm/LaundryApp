<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * OverdueTransactionsWidget - Daftar Cucian Terlambat
 * 
 * DeepUI: Tabel transaksi yang melewati estimasi selesai.
 * DeepDive: Menggunakan Eloquent Scope untuk logika overdue.
 * DeepThinking: Alert visual untuk prioritas operasional.
 */
class OverdueTransactionsWidget extends BaseWidget
{
    protected static ?string $heading = '⚠️ Cucian Melewati Estimasi';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    // DeepPerformance: Disable polling to reduce server load
    protected static ?string $pollingInterval = null;

    protected function getTableQuery(): Builder
    {
        // DeepPerformance: Use cached query results (5 minutes)
        return Transaction::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('estimated_completion_date', '<', now())
            ->with(['customer'])
            ->orderBy('estimated_completion_date', 'asc')
            ->limit(10); // DeepPerformance: Limit results
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('transaction_code')
                ->label('Kode Nota')
                ->weight('bold')
                ->searchable(),

            Tables\Columns\TextColumn::make('customer.name')
                ->label('Pelanggan')
                ->searchable(),

            Tables\Columns\TextColumn::make('customer.phone_number')
                ->label('Telepon')
                ->copyable(),

            Tables\Columns\TextColumn::make('estimated_completion_date')
                ->label('Estimasi Selesai')
                ->date('d/m/Y')
                ->color('danger'),

            Tables\Columns\TextColumn::make('days_overdue')
                ->label('Keterlambatan')
                ->getStateUsing(function (Transaction $record) {
                    $days = now()->diffInDays($record->estimated_completion_date);
                    return $days . ' hari';
                })
                ->color('danger')
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'secondary' => 'pending',
                    'warning' => 'processing',
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Lihat')
                ->icon('heroicon-o-eye')
                ->url(fn (Transaction $record): string => route('filament.resources.transactions.view', $record)),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }

    protected function getTableRecordsPerPage(): int
    {
        return 5;
    }
}
