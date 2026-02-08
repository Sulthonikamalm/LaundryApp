<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * CustomerResource - Filament Resource untuk Pelanggan
 * 
 * DeepUI: Interface untuk manajemen pelanggan.
 * DeepSecurity: Kasir bisa CRUD tapi tidak bisa delete.
 * 
 * @package App\Filament\Resources
 */
class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Pelanggan';

    protected static ?string $modelLabel = 'Pelanggan';

    protected static ?string $pluralModelLabel = 'Pelanggan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * Global search attributes.
     * 
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'phone_number', 'email'];
    }

    /**
     * Global search result details.
     * 
     * @param Customer $record
     * @return array<string, string>
     */
    public static function getGlobalSearchResultDetails($record): array
    {
        return [
            'Telepon' => $record->phone_number,
            'Tipe' => ucfirst($record->customer_type),
        ];
    }

    /**
     * Form schema.
     * 
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('No. Telepon')
                            ->required()
                            ->tel()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20)
                            ->helperText('Wajib dan harus unik'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),

                        Forms\Components\Select::make('customer_type')
                            ->label('Tipe Pelanggan')
                            ->options([
                                'individual' => 'Perorangan',
                                'corporate' => 'Perusahaan/Korporat',
                            ])
                            ->default('individual')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3)
                            ->columnSpanFull()
                            ->helperText('Alamat lengkap untuk pengiriman/penjemputan'),
                    ]),
            ]);
    }

    /**
     * Table schema.
     * 
     * @param Table $table
     * @return Table
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('Telepon')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-o-phone'),

                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('customer_type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'individual',
                        'success' => 'corporate',
                    ])
                    ->formatStateUsing(fn ($state) => $state === 'individual' ? 'Perorangan' : 'Korporat'),

                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('Total Transaksi')
                    ->counts('transactions')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Terdaftar')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('customer_type')
                    ->label('Tipe')
                    ->options([
                        'individual' => 'Perorangan',
                        'corporate' => 'Korporat',
                    ]),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->label('Lihat'),
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus')
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()->label('Hapus Terpilih')
                    ->visible(fn () => auth()->user()?->isOwner()),
                Tables\Actions\RestoreBulkAction::make()->label('Pulihkan')
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->defaultSort('name', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
