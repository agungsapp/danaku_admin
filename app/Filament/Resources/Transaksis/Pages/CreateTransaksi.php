<?php

namespace App\Filament\Resources\Transaksis\Pages;

use App\Filament\Resources\Transaksis\TransaksiResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksi extends CreateRecord
{
    // use \Filament\Resources\Pages\CreateRecord\Concerns\HasWizard;

    protected static string $resource = TransaksiResource::class;

    public function form(\Filament\Schemas\Schema $schema): \Filament\Schemas\Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Repeater::make('transaksis')
                    ->label('Daftar Transaksi')
                    ->schema([
                        \Filament\Forms\Components\Select::make('dompet_id')
                            ->label('Dompet')
                            ->relationship('dompet', 'nama', fn($query) => $query->where('user_id', auth()->id()))
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn() => \App\Models\Dompet::where('user_id', auth()->id())->first()?->id),
                            
                        \Filament\Forms\Components\Select::make('kategori_id')
                            ->label('Kategori')
                            ->relationship('kategori', 'nama', fn($query) => $query->where('user_id', auth()->id()))
                            ->required()
                            ->searchable()
                            ->preload(),

                        \Filament\Forms\Components\TextInput::make('judul')
                            ->required()
                            ->placeholder('Contoh: Makan Siang'),

                        \Filament\Forms\Components\TextInput::make('jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1),

                        \Filament\Forms\Components\Textarea::make('deskripsi')
                            ->rows(1)
                            ->columnSpanFull(),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    // ->grid(1)
                    ->addActionLabel('Tambah Transaksi Lain')
            ])->columns(1);
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $service = new \App\Services\TransactionService();
        $user = auth()->user();
        $lastTransaction = null;

        // Iterate through repeater data
        // Key 'transaksis' comes from repeater name
        $items = $data['transaksis'] ?? [];

        if (empty($items)) {
             // Fallback if form structure is different or empty
             // But with repeater required, this shouldn't happen usually
             $items = [$data];
        }

        foreach ($items as $item) {
            $lastTransaction = $service->createTransaction($user, $item);
        }

        return $lastTransaction;
    }
}