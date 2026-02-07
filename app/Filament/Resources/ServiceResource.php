<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

/**
 * ServiceResource - Filament Resource untuk Master Layanan
 * 
 * DeepSecurity: Kasir hanya bisa view, tidak bisa edit harga.
 * DeepUI: Interface untuk manajemen katalog layanan.
 * 
 * @package App\Filament\Resources
 */
class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = 'Layanan';

    protected static ?string $modelLabel = 'Layanan';

    protected static ?string $pluralModelLabel = 'Layanan';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'service_name';

    /**
     * Global search attributes.
     * 
     * @return array<string>
     */
    public static function getGloballySearchableAttributes(): array
    {
        return ['service_name', 'service_type'];
    }

    /**
     * Form schema.
     * 
     * DeepSecurity: Form ini hanya bisa diakses oleh Owner.
     * 
     * @param Form $form
     * @return Form
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Layanan')
                    ->schema([
                        Forms\Components\TextInput::make('service_name')
                            ->label('Nama Layanan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Cuci Kiloan Regular'),

                        Forms\Components\Select::make('service_type')
                            ->label('Tipe Layanan')
                            ->options([
                                'kiloan' => 'Kiloan (per kg)',
                                'satuan' => 'Satuan (per pcs)',
                                'express' => 'Express (cepat)',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('unit')
                            ->label('Satuan')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('kg atau pcs'),

                        Forms\Components\TextInput::make('base_price')
                            ->label('Harga Dasar')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0)
                            ->helperText('Harga per satuan (kg/pcs)'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Detail Tambahan')
                    ->schema([
                        Forms\Components\TextInput::make('estimated_duration_hours')
                            ->label('Estimasi Durasi (jam)')
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Contoh: 24'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Layanan Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan jika layanan tidak tersedia sementara'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
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
                Tables\Columns\TextColumn::make('service_name')
                    ->label('Nama Layanan')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('service_type')
                    ->label('Tipe')
                    ->colors([
                        'primary' => 'kiloan',
                        'success' => 'satuan',
                        'warning' => 'express',
                    ]),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan'),

                Tables\Columns\TextColumn::make('base_price')
                    ->label('Harga')
                    ->money('idr')
                    ->sortable(),

                Tables\Columns\TextColumn::make('estimated_duration_hours')
                    ->label('Durasi')
                    ->suffix(' jam')
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('service_type')
                    ->label('Tipe')
                    ->options([
                        'kiloan' => 'Kiloan',
                        'satuan' => 'Satuan',
                        'express' => 'Express',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status Aktif'),

                Tables\Filters\TrashedFilter::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
                Tables\Actions\RestoreBulkAction::make()
                    ->visible(fn () => auth()->user()?->isOwner()),
            ])
            ->defaultSort('service_name', 'asc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'view' => Pages\ViewService::route('/{record}'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    /**
     * Check if user can create.
     * 
     * DeepSecurity: Hanya Owner yang bisa menambah layanan.
     * 
     * @return bool
     */
    public static function canCreate(): bool
    {
        return auth()->user()?->isOwner() ?? false;
    }
}
