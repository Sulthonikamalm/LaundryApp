<?php

declare(strict_types=1);

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getActions(): array
    {
        $actions = [];

        // DeepSecurity: Delete hanya untuk Owner
        if (auth()->user()?->isOwner()) {
            $actions[] = Actions\DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Pelanggan')
                ->modalButton('Ya, Hapus');
                
            $actions[] = Actions\ForceDeleteAction::make()
                ->label('Hapus Permanen')
                ->modalHeading('Hapus Permanen Pelanggan')
                ->modalButton('Ya, Hapus Selamanya');
                
            $actions[] = Actions\RestoreAction::make()
                ->label('Pulihkan')
                ->modalButton('Ya, Pulihkan');
        }

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getFormActions(): array
    {
        return [
            $this->getSaveFormAction()->label('Simpan Perubahan'),
            $this->getCancelFormAction()->label('Batal'),
        ];
    }
}
