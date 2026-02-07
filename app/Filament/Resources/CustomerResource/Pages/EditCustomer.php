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
            $actions[] = Actions\DeleteAction::make();
            $actions[] = Actions\ForceDeleteAction::make();
            $actions[] = Actions\RestoreAction::make();
        }

        return $actions;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
