<?php

namespace App\Filament\Resources\Dompets;

use App\Filament\Resources\Dompets\Pages\ManageDompets;
use App\Models\Dompet;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DompetResource extends Resource
{
    protected static ?string $model = Dompet::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $navigationLabel = 'Dompet';
    protected static ?string $modelLabel = 'Dompet';
    protected static ?string $pluralModelLabel = 'Dompet';
    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return 'Keuangan';
    }

    // User scoping: regular users only see their own wallets
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        
        if (!auth()->user()->isAdmin()) {
            $query->where('user_id', auth()->id());
        }
        
        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Dompet')
                    ->schema([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Tabungan, Uang Harian, Dana Darurat'),
                        
                        TextInput::make('saldo')
                            ->required()
                            ->numeric()
                            ->default(0)
                            ->prefix('Rp')
                            ->disabled() // Saldo diupdate otomatis dari transaksi
                            ->dehydrated()
                            ->helperText('Saldo akan otomatis terupdate dari transaksi'),
                        
                        Textarea::make('deskripsi')
                            ->rows(3)
                            ->placeholder('Deskripsi opsional untuk dompet ini')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfoSection::make('Detail Dompet')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Pemilik')
                            ->visible(fn() => auth()->user()->isAdmin()),
                        
                        TextEntry::make('nama')
                            ->label('Nama Dompet'),
                        
                        TextEntry::make('saldo')
                            ->label('Saldo')
                            ->money('IDR')
                            ->badge()
                            ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
                        
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi')
                            ->placeholder('-')
                            ->columnSpanFull(),
                        
                        TextEntry::make('transaksis_count')
                            ->label('Total Transaksi')
                            ->counts('transaksis')
                            ->badge()
                            ->color('info'),
                        
                        TextEntry::make('created_at')
                            ->label('Dibuat')
                            ->dateTime('d M Y H:i'),
                        
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diupdate')
                            ->dateTime('d M Y H:i'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Pemilik')
                    ->sortable()
                    ->searchable()
                    ->visible(fn() => auth()->user()->isAdmin())
                    ->toggleable(),
                
                TextColumn::make('nama')
                    ->label('Nama Dompet')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('saldo')
                    ->label('Saldo')
                    ->money('IDR')
                    ->sortable()
                    ->badge()
                    ->color(fn($state) => $state >= 0 ? 'success' : 'danger'),
                
                TextColumn::make('transaksis_count')
                    ->label('Transaksi')
                    ->counts('transaksis')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Diupdate')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->label('Pemilik')
                    ->relationship('user', 'name')
                    ->visible(fn() => auth()->user()->isAdmin())
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDompets::route('/'),
        ];
    }
}
