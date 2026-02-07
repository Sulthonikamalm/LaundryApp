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
            $actions[] = Actions\DeleteAction::make();
            $actions[] = Actions\ForceDeleteAction::make();
            $actions[] = Actions\RestoreAction::make();
        }

        return $actions;
    }

    /**
     * Redirect after save.
     * 
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
