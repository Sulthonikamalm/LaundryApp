<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Exports\TransactionsExport;
use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions;
use Maatwebsite\Excel\Facades\Excel;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getActions(): array
    {
        $actions = [
            Actions\CreateAction::make()
                ->label('Buat Transaksi')
                ->icon('heroicon-o-plus'),
        ];

        // DeepSecurity: Export hanya untuk Owner
        if (auth()->user()?->isOwner()) {
            $actions[] = Actions\Action::make('export')
                ->label('Export Excel')
                ->icon('heroicon-o-download')
                ->color('success')
                ->action(function () {
                    $filename = 'transaksi_' . now()->format('Y-m-d_His') . '.xlsx';
                    
                    // Export bulan ini
                    $startDate = now()->startOfMonth()->format('Y-m-d');
                    $endDate = now()->format('Y-m-d');
                    
                    return Excel::download(
                        (new TransactionsExport())->forDateRange($startDate, $endDate),
                        $filename
                    );
                });
        }

        return $actions;
    }
}
