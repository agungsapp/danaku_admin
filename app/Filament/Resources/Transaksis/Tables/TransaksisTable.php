<?php

namespace App\Filament\Resources\Transaksis\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TransaksisTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->visible(fn() => auth()->user()->isAdmin()),
                    
                TextColumn::make('dompet.nama')
                    ->label('Dompet')
                    ->sortable()
                    ->searchable(),
                    
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn($record) => $record->kategori->tipe === 'in' ? 'success' : 'danger'),

                TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->limit(30),

                TextColumn::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable()
                    ->color(fn($record) => $record->kategori->tipe === 'in' ? 'success' : 'danger'),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('dompet_id')
                    ->label('Dompet')
                    ->relationship('dompet', 'nama', fn($query) => auth()->user()->isAdmin() ? $query : $query->where('user_id', auth()->id()))
                    ->searchable()
                    ->preload(),
                    
                \Filament\Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama', fn($query) => auth()->user()->isAdmin() ? $query : $query->where('user_id', auth()->id()))
                    ->searchable()
                    ->preload(),
                    
                \Filament\Tables\Filters\SelectFilter::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->visible(fn() => auth()->user()->isAdmin())
                    ->searchable()
                    ->preload(),
                    
                \Filament\Tables\Filters\Filter::make('created_at')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('created_from')->label('Dari Tanggal'),
                        \Filament\Forms\Components\DatePicker::make('created_until')->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                \Filament\Actions\DeleteAction::make()
                    ->action(fn(\App\Models\Transaksi $record) => (new \App\Services\TransactionService())->deleteTransaction($record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $service = new \App\Services\TransactionService();
                            foreach ($records as $record) {
                                $service->deleteTransaction($record);
                            }
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(function (Builder $query) {
                if (!auth()->user()->isAdmin()) {
                    $query->where('user_id', auth()->id());
                }
                return $query;
            });
    }
}
