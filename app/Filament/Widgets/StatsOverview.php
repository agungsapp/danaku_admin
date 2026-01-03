<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $user = auth()->user();

        $totalSaldo = \App\Models\Dompet::where('user_id', $user->id)->sum('saldo');

        $pemasukan = \App\Models\Transaksi::where('user_id', $user->id)
            ->whereHas('kategori', function ($query) {
                $query->where('tipe', 'in');
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');

        $pengeluaran = \App\Models\Transaksi::where('user_id', $user->id)
            ->whereHas('kategori', function ($query) {
                $query->where('tipe', 'out');
            })
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('jumlah');

        return [
            Stat::make('Total Saldo', 'Rp ' . number_format($totalSaldo, 0, ',', '.'))
                ->description('Total uang di semua dompet')
                ->descriptionIcon('heroicon-m-wallet')
                ->color('primary'),

            Stat::make('Pemasukan Bulan Ini', 'Rp ' . number_format($pemasukan, 0, ',', '.'))
                ->description('Total pemasukan bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Pengeluaran Bulan Ini', 'Rp ' . number_format($pengeluaran, 0, ',', '.'))
                ->description('Total pengeluaran bulan ini')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->color('danger')
                ->chart([17, 4, 15, 3, 10, 2, 7]),
        ];
    }
}
