<?php

namespace App\Filament\Widgets;

use App\Models\Transaction;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

/**
 * LatestTransactionsWidget - Actionable Dashboard Table
 * 
 * DeepUI: Menampilkan transaksi yang MEMBUTUHKAN TINDAKAN segera.
 * Kriteria:
 * 1. Status 'ready' (Siap diambil tapi belum diambil)
 * 2. Status 'pending'/'processing' TAPI sudah lewat estimasi selesai (Overdue)
 */
class LatestTransactionsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 6,
    ];

    protected static ?string $heading = '⚠️ Perlu Tindakan Segera';

    // DeepUI: Penjelasan konteks tabel
    protected function getTableDescription(): ?string
    {
        return 'Daftar cucian yang sudah selesai (siap diambil) atau terlambat diproses.';
    }

    protected function getTableQuery(): Builder
    {
        return Transaction::query()
            ->where('status', 'ready')
            ->orWhere(function (Builder $query) {
                $query->whereIn('status', ['pending', 'processing'])
                      ->where('estimated_completion_date', '<', now());
            })
            ->with(['customer']);
    }

    // DeepPriority: Urutkan berdasarkan deadline terlama (yg paling terlambat/lama menunggu)
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'estimated_completion_date';
    }

    protected function getDefaultTableSortDirection(): ?string
    {
        return 'asc';
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('transaction_code')
                ->label('Kode')
                ->weight('bold')
                ->searchable()
                ->copyable(),

            Tables\Columns\TextColumn::make('customer.name')
                ->label('Pelanggan')
                ->searchable()
                ->description(fn (Transaction $record) => $record->customer->phone_number),

            Tables\Columns\BadgeColumn::make('status')
                ->label('Status')
                ->colors([
                    'success' => 'ready',
                    'warning' => 'pending',
                    'primary' => 'processing',
                    'danger' => 'cancelled', // Just in case
                ])
                ->formatStateUsing(fn (string $state): string => match ($state) {
                    'ready' => 'Siap Diambil',
                    'pending' => 'Menunggu',
                    'processing' => 'Diproses',
                    default => ucfirst($state),
                }),

            Tables\Columns\TextColumn::make('estimated_completion_date')
                ->label('Deadline / Estimasi')
                ->date('d M Y, H:i')
                // DeepAlert: Merah jika sudah lewat waktu sekarang
                ->color(fn (Transaction $record) => 
                    $record->estimated_completion_date < now() ? 'danger' : 'success'
                )
                ->weight('medium')
                ->description(function (Transaction $record) {
                    if ($record->status === 'ready') {
                        // Menunggu diambil berapa lama?
                        return 'Menunggu diambil pelanggan';
                    }
                    // Overdue context
                    $days = now()->diffInDays($record->estimated_completion_date);
                    return $record->estimated_completion_date < now() 
                        ? "Terlambat {$days} hari!" 
                        : null;
                }),
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
                
            // Action cepat: Tandai Selesai (jika status ready)
            Tables\Actions\Action::make('complete')
                ->label('Ambil')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn (Transaction $record) => $record->status === 'ready')
                ->action(function (Transaction $record) {
                    $record->update(['status' => 'completed']);
                    // Reload widget logic if possible or just let page refresh
                })
                ->requiresConfirmation(),
        ];
    }
}
