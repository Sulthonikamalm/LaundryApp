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
    protected $paymentData = [];

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
        
        // Extract payment data
        if (isset($data['virtual_payment_amount'])) {
            $this->paymentData['amount'] = (float) $data['virtual_payment_amount'];
            unset($data['virtual_payment_amount']);
        }
        
        if (isset($data['virtual_payment_method'])) {
            $this->paymentData['method'] = $data['virtual_payment_method'];
            unset($data['virtual_payment_method']);
        }

        return $data;
    }

    /**
     * Handle after create hook.
     */
    protected function afterCreate(): void
    {
        // 0. DeepFix: Recalculate Total Cost from Details
        // Trust the DB source of truth (sum of details), not the form input
        $this->record->recalculateTotalCost();
        $this->record->refresh();

        // 1. Handle Initial Payment if amount > 0
        if (!empty($this->paymentData['amount']) && $this->paymentData['amount'] > 0) {
            \App\Models\Payment::create([
                'transaction_id' => $this->record->id,
                'amount' => $this->paymentData['amount'],
                'payment_method' => $this->paymentData['method'] ?? 'cash',
                'status' => 'completed',
                'payment_date' => now(),
                'processed_by' => auth()->id(),
                'notes' => 'Pembayaran Awal (DP/Lunas)',
            ]);
            
            // Reload transaction again to get updated paid_amount & payment_status
            $this->record->refresh();
        }

        // 2. Dispatch WhatsApp Job (Correct Place)
        // DeepFix: Kirim setelah semua data siap (termasuk payment)
        \App\Jobs\SendWhatsappJob::dispatch($this->record, 'new_order');
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
