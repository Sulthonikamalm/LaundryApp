<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * OverdueTransactionsWidget - Alert Cucian Terlambat
 * 
 * DeepUI: Widget untuk menampilkan transaksi yang melewati estimasi selesai.
 * DeepFix: Menggunakan API Filament v2 yang benar.
 */
class OverdueTransactionsWidget extends BaseWidget
{
    // DeepUI: Judul widget (tampil di header card)
    protected static ?string $heading = '⚠️ Cucian Melewati Estimasi';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    // DeepPerformance: No polling
    protected static ?string $pollingInterval = null;

    /**
     * DeepFix: Dummy method untuk backward compatibility
     */
    public function loadWidget(): void {}

    /**
     * DeepUI: Penjelasan singkat di bawah heading
     */
    protected function getTableDescription(): ?string
    {
        return 'Transaksi yang melewati tanggal estimasi selesai. Prioritas tinggi!';
    }

    protected function getTableQuery(): Builder
    {
        return Transaction::query()
            ->whereIn('status', ['pending', 'processing'])
            ->where('estimated_completion_date', '<', now())
            ->with(['customer:id,name,phone_number'])
            ->orderBy('estimated_completion_date', 'asc')
            ->limit(10);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('transaction_code')
                ->label('Kode')
                ->weight('bold')
                ->searchable()
                ->sortable(),

            Tables\Columns\TextColumn::make('customer.name')
                ->label('Pelanggan')
                ->searchable(),

            Tables\Columns\TextColumn::make('customer.phone_number')
                ->label('Telepon')
                ->copyable()
                ->icon('heroicon-o-phone'),

            Tables\Columns\TextColumn::make('estimated_completion_date')
                ->label('Deadline')
                ->date('d M Y')
                ->color('danger')
                ->sortable(),

            Tables\Columns\TextColumn::make('days_overdue')
                ->label('Terlambat')
                ->getStateUsing(function (Transaction $record) {
                    $days = now()->diffInDays($record->estimated_completion_date);
                    return $days . ' hari';
                })
                ->color('danger')
                ->weight('bold'),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'warning' => 'pending',
                    'primary' => 'processing',
                ]),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('view')
                ->label('Detail')
                ->icon('heroicon-o-eye')
                ->url(fn (Transaction $record): string => 
                    route('filament.resources.transactions.view', $record)
                ),
        ];
    }

    // DeepPerformance: Disable pagination (no COUNT query)
    protected function isTablePaginationEnabled(): bool
    {
        return false;
    }
}
