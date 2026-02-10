<?php

namespace App\Filament\Resources\PendingPaymentResource\Pages;

use App\Filament\Resources\PendingPaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPendingPayments extends ListRecords
{
    protected static string $resource = PendingPaymentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('refresh')
                ->label('Refresh')
                ->icon('heroicon-o-refresh')
                ->action('$refresh'),
        ];
    }

    /**
     * Auto-refresh every 30 seconds
     * 
     * @return int
     */
    protected function getPollingInterval(): ?string
    {
        return '30s';
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Add stats widget here if needed
        ];
    }
}
