<?php

namespace App\Filament\Resources\Transaksis\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TransaksiForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('dompet_id')
                    ->label('Dompet')
                    ->relationship('dompet', 'nama', fn($query) => $query->where('user_id', auth()->id()))
                    ->required()
                    ->searchable()
                    ->preload(),
                    
                \Filament\Forms\Components\Select::make('kategori_id')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama', fn($query) => $query->where('user_id', auth()->id()))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->createOptionForm([
                        \Filament\Forms\Components\TextInput::make('nama')->required(),
                        \Filament\Forms\Components\Select::make('tipe')
                            ->options(['in' => 'Pemasukan', 'out' => 'Pengeluaran'])
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data) {
                        $data['user_id'] = auth()->id();
                        return \App\Models\Kategori::create($data)->id;
                    }),

                \Filament\Forms\Components\TextInput::make('judul')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Contoh: Makan Siang'),

                \Filament\Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->minValue(1),

                \Filament\Forms\Components\Textarea::make('deskripsi')
                    ->columnSpanFull(),
            ]);
    }
}
