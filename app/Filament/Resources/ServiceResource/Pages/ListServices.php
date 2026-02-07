<?php

declare(strict_types=1);

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getActions(): array
    {
        $actions = [];

        // DeepSecurity: Tombol Create hanya untuk Owner
        if (auth()->user()?->isOwner()) {
            $actions[] = Actions\CreateAction::make();
        }

        return $actions;
    }
}
