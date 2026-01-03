<?php

namespace App\Filament\Resources\Kategoris;

use App\Filament\Resources\Kategoris\Pages\ManageKategoris;
use App\Models\Kategori;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class KategoriResource extends Resource
{
    protected static ?string $model = Kategori::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordTitleAttribute = 'nama';
    protected static ?string $navigationLabel = 'Kategori';
    protected static ?string $modelLabel = 'Kategori';
    protected static ?string $pluralModelLabel = 'Kategori';
    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return 'Keuangan';
    }

    // User scoping: regular users only see their own categories
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
                Section::make('Informasi Kategori')
                    ->schema([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Gaji, Transportasi, Makanan'),
                        
                        Select::make('tipe')
                            ->label('Tipe Kategori')
                            ->options([
                                'in' => 'Pemasukan',
                                'out' => 'Pengeluaran',
                            ])
                            ->required()
                            ->native(false)
                            ->helperText('Pilih Pemasukan untuk uang masuk, Pengeluaran untuk uang keluar'),
                    ])
                    ->columns(2),
            ])->columns(1);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                InfoSection::make('Detail Kategori')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Pemilik')
                            ->visible(fn() => auth()->user()->isAdmin()),
                        
                        TextEntry::make('nama')
                            ->label('Nama Kategori'),
                        
                        TextEntry::make('tipe')
                            ->label('Tipe')
                            ->badge()
                            ->color(fn($state) => $state === 'in' ? 'success' : 'danger')
                            ->formatStateUsing(fn($state) => $state === 'in' ? 'Pemasukan' : 'Pengeluaran'),
                        
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
                    ->label('Nama Kategori')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                TextColumn::make('tipe')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn($state) => $state === 'in' ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state === 'in' ? 'Pemasukan' : 'Pengeluaran')
                    ->sortable(),
                
                TextColumn::make('transaksis_count')
                    ->label('Transaksi')
                    ->counts('transaksis')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
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
                SelectFilter::make('tipe')
                    ->label('Tipe')
                    ->options([
                        'in' => 'Pemasukan',
                        'out' => 'Pengeluaran',
                    ])
                    ->native(false),
                
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
            'index' => ManageKategoris::route('/'),
        ];
    }
}
