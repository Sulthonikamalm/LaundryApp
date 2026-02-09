<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\Actions;

class EditTransaction extends EditRecord
{
    protected static string $resource = TransactionResource::class;

    protected function getActions(): array
    {
        $actions = [];

        // DeepSecurity: Tombol delete hanya muncul untuk Owner
        if (auth()->user()?->isOwner()) {
            $actions[] = Actions\DeleteAction::make()
                ->label('Hapus')
                ->modalHeading('Hapus Transaksi')
                ->modalButton('Ya, Hapus');
            $actions[] = Actions\ForceDeleteAction::make()
                ->label('Hapus Permanen')
                ->modalHeading('Hapus Permanen')
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
            $this->getSaveFormAction()
                ->label('Simpan Perubahan')
                ->icon('heroicon-o-check'),
            $this->getCancelFormAction()
                ->label('Batal')
                ->icon('heroicon-o-x'),
        ];
    }
}
