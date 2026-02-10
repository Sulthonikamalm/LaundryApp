<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\PendingPaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

/**
 * PendingPaymentResource - Admin approval untuk demo payments
 * 
 * DeepWorkflow: Manual approval untuk demo mode
 * DeepAudit: Track approval dengan admin_id dan timestamp
 */
class PendingPaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Verifikasi Pembayaran';

    protected static ?string $modelLabel = 'Pembayaran Pending';

    protected static ?string $pluralModelLabel = 'Verifikasi Pembayaran';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 2;

    /**
     * Get eloquent query with pending payments only
     * 
     * @return Builder
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('gateway_status', 'pending')
            ->with(['transaction.customer'])
            ->latest();
    }

    /**
     * Get navigation badge (count pending payments)
     * 
     * @return string|null
     */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getEloquentQuery()->count();
        return $count > 0 ? (string) $count : null;
    }

    /**
     * Get navigation badge color
     * 
     * @return string|null
     */
    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Placeholder::make('transaction_code')
                            ->label('Kode Nota')
                            ->content(fn ($record) => $record->transaction->transaction_code ?? '-'),

                        Forms\Components\Placeholder::make('customer_name')
                            ->label('Pelanggan')
                            ->content(fn ($record) => $record->transaction->customer->name ?? '-'),

                        Forms\Components\Placeholder::make('amount')
                            ->label('Jumlah')
                            ->content(fn ($record) => 'Rp ' . number_format($record->amount, 0, ',', '.')),

                        Forms\Components\Placeholder::make('payment_method')
                            ->label('Metode')
                            ->content(fn ($record) => strtoupper($record->payment_method)),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('Waktu Pembayaran')
                            ->content(fn ($record) => $record->created_at->format('d M Y, H:i')),

                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan (Opsional)')
                            ->rows(3)
                            ->visible(fn ($livewire) => $livewire instanceof \Filament\Resources\Pages\EditRecord),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction.transaction_code')
                    ->label('Kode Nota')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                Tables\Columns\TextColumn::make('transaction.customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('idr')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('payment_method')
                    ->label('Metode')
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->colors([
                        'primary' => 'qris',
                        'success' => 'cash',
                        'warning' => 'transfer',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('gateway_status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Approve Action
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran')
                    ->modalSubheading(fn (Payment $record) => 
                        "Apakah Anda yakin ingin menyetujui pembayaran sebesar Rp " . 
                        number_format($record->amount, 0, ',', '.') . 
                        " dari {$record->transaction->customer->name}?"
                    )
                    ->modalButton('Ya, Setujui')
                    ->action(function (Payment $record) {
                        $record->update([
                            'gateway_status' => 'approved',
                            'status' => 'completed', // This will trigger Payment observer
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body("Pembayaran {$record->transaction->transaction_code} telah disetujui.")
                            ->success()
                            ->send();

                        // Optional: Send WhatsApp notification to customer
                        // \App\Jobs\SendWhatsappJob::dispatch($record->transaction, 'payment_approved');
                    })
                    ->visible(fn (Payment $record) => $record->gateway_status === 'pending'),

                // Reject Action
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pembayaran')
                    ->modalSubheading('Apakah Anda yakin ingin menolak pembayaran ini?')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3)
                            ->placeholder('Contoh: Bukti transfer tidak valid, nominal tidak sesuai, dll.'),
                    ])
                    ->modalButton('Ya, Tolak')
                    ->action(function (Payment $record, array $data) {
                        $record->update([
                            'gateway_status' => 'rejected',
                            'status' => 'failed',
                            'approved_by' => auth()->id(),
                            'approved_at' => now(),
                            'rejection_reason' => $data['rejection_reason'],
                        ]);

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Ditolak')
                            ->body("Pembayaran {$record->transaction->transaction_code} telah ditolak.")
                            ->warning()
                            ->send();

                        // Optional: Send WhatsApp notification to customer
                        // \App\Jobs\SendWhatsappJob::dispatch($record->transaction, 'payment_rejected');
                    })
                    ->visible(fn (Payment $record) => $record->gateway_status === 'pending'),

                Tables\Actions\ViewAction::make()->label('Detail'),
            ])
            ->bulkActions([
                // Bulk Approve
                Tables\Actions\BulkAction::make('bulk_approve')
                    ->label('Setujui Semua')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pembayaran Terpilih')
                    ->modalSubheading('Apakah Anda yakin ingin menyetujui semua pembayaran yang dipilih?')
                    ->modalButton('Ya, Setujui Semua')
                    ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                        $records->each(function (Payment $record) {
                            $record->update([
                                'gateway_status' => 'approved',
                                'status' => 'completed',
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);
                        });

                        \Filament\Notifications\Notification::make()
                            ->title('Pembayaran Disetujui')
                            ->body("{$records->count()} pembayaran telah disetujui.")
                            ->success()
                            ->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPendingPayments::route('/'),
            'view' => Pages\ViewPendingPayment::route('/{record}'),
        ];
    }
}
