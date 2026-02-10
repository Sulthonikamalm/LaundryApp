<?php

namespace App\Filament\Resources\PendingPaymentResource\Pages;

use App\Filament\Resources\PendingPaymentResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPendingPayment extends ViewRecord
{
    protected static string $resource = PendingPaymentResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Kembali')
                ->url(static::getResource()::getUrl('index'))
                ->color('secondary'),
        ];
    }
}
