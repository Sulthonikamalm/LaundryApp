<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Models\Service;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * TransactionResource - Core Transaction Logic
 * 
 * DeepUI: Optimized for quick data entry (Pos-like experience)
 * DeepSecurity: Strict validation and read-only price handling
 * DeepState: Reactive calculation for subtotals and totals
 */
class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Transaksi';

    protected static ?string $modelLabel = 'Transaksi';

    protected static ?string $pluralModelLabel = 'Transaksi';

    protected static ?string $navigationGroup = 'Operasional';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'transaction_code';

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'transaction_code',
            'customer.name',
            'customer.phone_number',
        ];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Pelanggan' => $record->customer?->name ?? 'Unknown',
            'Status' => ucfirst($record->status),
            'Total' => 'Rp ' . number_format($record->total_cost, 0, ',', '.'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()
            ->with(['customer']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        // SECTION 1: HEADER TRANSAKSI
                        Card::make()
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        TextInput::make('transaction_code')
                                            ->label('Kode Nota')
                                            ->disabled()
                                            ->dehydrated()
                                            ->default(fn () => Transaction::generateTransactionCode()),

                                        Select::make('customer_id')
                                            ->label('Pelanggan')
                                            ->relationship('customer', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            // DeepUI: Create customer on the fly
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('phone_number')
                                                    ->required()
                                                    ->tel()
                                                    ->maxLength(20),
                                                Select::make('customer_type')
                                                    ->options([
                                                        'individual' => 'Perorangan',
                                                        'corporate' => 'Perusahaan',
                                                    ])
                                                    ->default('individual')
                                                    ->required(),
                                                Textarea::make('address')
                                                    ->rows(2),
                                            ])
                                            ->createOptionAction(function (Forms\Components\Actions\Action $action) {
                                                return $action
                                                    ->modalHeading('Tambah Pelanggan Baru')
                                                    ->modalButton('Simpan Pelanggan')
                                                    ->modalWidth('lg');
                                            }),
                                    ]),

                                Grid::make(2)
                                    ->schema([
                                        DatePicker::make('order_date')
                                            ->label('Tanggal Order')
                                            ->default(now())
                                            ->required(),

                                        DatePicker::make('estimated_completion_date')
                                            ->label('Estimasi Selesai')
                                            ->default(now()->addDays(2)) // Default 2 hari kerja
                                            ->required()
                                            ->minDate(now()),
                                    ]),
                            ]),

                        // SECTION 2: DETAIL LAYANAN (REPEATER)
                        Section::make('Detail Layanan')
                            ->schema([
                                Repeater::make('details')
                                    ->relationship()
                                    ->schema([
                                        Grid::make(3) // Layout 3 kolom untuk form repeater
                                            ->schema([
                                                Select::make('service_id')
                                                    ->label('Layanan')
                                                    ->options(Service::where('is_active', true)->pluck('service_name', 'id'))
                                                    ->required()
                                                    ->searchable()
                                                    ->reactive() // DeepState: React to changes
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        // DeepSafety: Auto-fetch price from master
                                                        if ($state) {
                                                            $service = Service::find($state);
                                                            if ($service) {
                                                                $set('price_at_transaction', $service->base_price);
                                                                $set('unit', $service->unit);
                                                                
                                                                // Recalculate subtotal
                                                                $qty = (float) $get('quantity');
                                                                $price = (float) $service->base_price;
                                                                $set('subtotal', $qty * $price);
                                                            }
                                                        }
                                                    })
                                                    ->columnSpan(1), // Use 1 column

                                                TextInput::make('quantity')
                                                    ->label('Jumlah / Berat')
                                                    ->numeric()
                                                    ->default(1)
                                                    ->minValue(0.1)
                                                    ->required()
                                                    ->reactive()
                                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                                        // DeepState: Recalculate subtotal
                                                        $qty = (float) $state;
                                                        $price = (float) $get('price_at_transaction');
                                                        $set('subtotal', $qty * $price);
                                                    })
                                                    ->columnSpan(1), // Use 1 column

                                                TextInput::make('price_at_transaction')
                                                    ->label('Harga (@)')
                                                    ->numeric()
                                                    ->disabled() // DeepSecurity: Lock price from editing
                                                    ->dehydrated() // Ensure it is sent to server
                                                    ->required()
                                                    ->suffix('IDR')
                                                    ->columnSpan(1), // Use 1 column
                                            ]),

                                        // Row bawah untuk Subtotal visual
                                        Grid::make(1)
                                            ->schema([
                                                TextInput::make('subtotal')
                                                    ->label('Subtotal')
                                                    ->disabled()
                                                    ->numeric()
                                                    ->prefix('Rp')
                                                    ->dehydrated() // Perlu dikirim untuk validasi/display
                                            ]),
                                    ])
                                    ->columns(1)
                                    // DeepState: Recalculate Grand Total when items change
                                    // Note: In Filament v2/v3 logic might require server-side hook or dedicated placeholder
                                    ->collapsible()
                                    ->cloneable()
                                    ->defaultItems(1)
                                    ->minItems(1),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                // SECTION 3: SIDEBAR STATUS & TOTAL
                Group::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                Select::make('status')
                                    ->label('Status Order')
                                    ->options([
                                        'pending' => 'Pending',
                                        'processing' => 'Proses',
                                        'ready' => 'Siap Diambil',
                                        'completed' => 'Selesai',
                                        'cancelled' => 'Dibatalkan',
                                    ])
                                    ->default('pending')
                                    ->required()
                                    ->disableOptionWhen(fn (string $value): bool => $value === 'completed' && ! auth()->user()->isOwner()),
                                
                                Select::make('payment_status')
                                    ->label('Status Pembayaran')
                                    ->options([
                                        'unpaid' => 'Belum Bayar',
                                        'partial' => 'DP/Sebagian',
                                        'paid' => 'Lunas',
                                    ])
                                    ->disabled() // Managed by system
                                    ->default('unpaid'),

                                // Placeholder for Total Cost visualization
                                Placeholder::make('total_cost_placeholder')
                                    ->label('Estimasi Total')
                                    ->content(fn ($record) => $record ? 'Rp ' . number_format((float) $record->total_cost, 0, ',', '.') : 'Hitung otomatis setelah simpan'),
                                    
                                TextInput::make('total_cost')
                                    ->label('Total Tagihan')
                                    ->disabled() // DeepSecurity: Users cannot manually set total
                                    ->dehydrated()
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Section::make('Catatan')
                            ->schema([
                                Textarea::make('customer_notes')
                                    ->label('Catatan Pelanggan')
                                    ->rows(3),
                                Textarea::make('internal_notes')
                                    ->label('Catatan Internal')
                                    ->rows(3),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('transaction_code')
                    ->label('Kode Nota')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),

                TextColumn::make('customer.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('customer.phone_number')
                    ->label('Telepon')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('order_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Status') // Translation map di model/resource
                    ->colors([
                        'secondary' => 'pending',
                        'warning' => 'processing',
                        'success' => 'ready',
                        'primary' => 'completed',
                        'danger' => 'cancelled',
                    ]),

                BadgeColumn::make('payment_status')
                    ->label('Pembayaran')
                    ->colors([
                        'danger' => 'unpaid',
                        'warning' => 'partial',
                        'success' => 'paid',
                    ]),

                TextColumn::make('total_cost')
                    ->label('Total')
                    ->money('idr')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Proses',
                        'ready' => 'Siap',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    ]),
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus')
                    ->hidden(fn () => !auth()->user()->isOwner()),
            ])
            ->defaultSort('created_at', 'desc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'view' => Pages\ViewTransaction::route('/{record}'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['customer']); // DeepPerformance: Only load necessary relations for list view
    }
}
