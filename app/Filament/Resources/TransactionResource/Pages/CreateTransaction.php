<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    /**
     * Disable "Create & Create Another" button.
     * 
     * DeepUX: Fokus kasir pada satu transaksi sampai selesai.
     * Mencegah double entry yang tidak disengaja.
     * 
     * @return bool
     */
    protected static bool $canCreateAnother = false;

    /**
     * Mutate form data before create.
     * 
     * DeepSecurity: Auto-set created_by dengan user yang sedang login.
     * Deepsecrethacking: Ini penting untuk audit trail.
     * 
     * @param array $data
     * @return array
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        
        return $data;
    }

    /**
     * Redirect after create.
     * 
     * @return string
     */
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Custom Form Actions (Translating to Indonesian).
     * 
     * @return array
     */
    protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan Transaksi')
                ->icon('heroicon-o-check'),
            $this->getCancelFormAction()
                ->label('Batal')
                ->icon('heroicon-o-x'),
        ];
    }
}
