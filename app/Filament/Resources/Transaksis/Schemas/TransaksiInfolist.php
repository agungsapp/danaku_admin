<?php

namespace App\Filament\Resources\Transaksis\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TransaksiInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Infolists\Components\TextEntry::make('user.name')
                    ->label('User')
                    ->visible(fn() => auth()->user()->isAdmin()),
                \Filament\Infolists\Components\TextEntry::make('dompet.nama')
                    ->label('Dompet'),
                \Filament\Infolists\Components\TextEntry::make('kategori.nama')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn($record) => $record->kategori->tipe === 'in' ? 'success' : 'danger'),
                \Filament\Infolists\Components\TextEntry::make('judul')
                    ->label('Judul'),
                \Filament\Infolists\Components\TextEntry::make('jumlah')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->color(fn($record) => $record->kategori->tipe === 'in' ? 'success' : 'danger'),
                \Filament\Infolists\Components\TextEntry::make('deskripsi')
                    ->label('Deskripsi')
                    ->placeholder('-')
                    ->columnSpanFull(),
                \Filament\Infolists\Components\TextEntry::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
                \Filament\Infolists\Components\TextEntry::make('updated_at')
                    ->label('Terakhir Diupdate')
                    ->dateTime('d M Y H:i')
                    ->placeholder('-'),
            ]);
    }
}
