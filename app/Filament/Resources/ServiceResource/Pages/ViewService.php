<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Pages\Actions;

class ViewService extends ViewRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getActions(): array
    {
        $actions = [];

        if (auth()->user()?->isOwner()) {
            $actions[] = Actions\EditAction::make();
        }

        return $actions;
    }
}
