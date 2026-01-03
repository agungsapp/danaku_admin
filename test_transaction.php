<?php

// Test script untuk verifikasi TransactionService
use App\Models\User;
use App\Services\TransactionService;

echo "=== Testing Transaction Service ===\n\n";

// Get user test (user_id = 2)
$user = User::find(2);
echo "User: {$user->name} (Role: {$user->role})\n";
echo "Dompets: {$user->dompets->count()}\n";
echo "Kategoris: {$user->kategoris->count()}\n\n";

// Get dompet dan kategori
$dompet = $user->dompets->first();
$kategoriIn = $user->kategoris->where('tipe', 'in')->first();
$kategoriOut = $user->kategoris->where('tipe', 'out')->first();

echo "Dompet: {$dompet->nama} (Saldo awal: Rp " . number_format($dompet->saldo) . ")\n";
echo "Kategori IN: {$kategoriIn->nama}\n";
echo "Kategori OUT: {$kategoriOut->nama}\n\n";

// Test create transaksi IN (menambah saldo)
$service = new TransactionService();

echo "--- Test Transaksi IN (Gaji) ---\n";
$transaksi1 = $service->createTransaction($user, [
    'dompet_id' => $dompet->id,
    'kategori_id' => $kategoriIn->id,
    'judul' => 'Gaji Bulanan',
    'jumlah' => 5000000,
    'deskripsi' => 'Gaji bulan Januari 2026'
]);

$dompet->refresh();
echo "Transaksi created: {$transaksi1->judul} - Rp " . number_format($transaksi1->jumlah) . "\n";
echo "Saldo setelah IN: Rp " . number_format($dompet->saldo) . "\n\n";

// Test create transaksi OUT (mengurangi saldo)
echo "--- Test Transaksi OUT (Transportasi) ---\n";
$transaksi2 = $service->createTransaction($user, [
    'dompet_id' => $dompet->id,
    'kategori_id' => $kategoriOut->id,
    'judul' => 'Bensin Motor',
    'jumlah' => 50000,
    'deskripsi' => 'Isi bensin untuk ke kantor'
]);

$dompet->refresh();
echo "Transaksi created: {$transaksi2->judul} - Rp " . number_format($transaksi2->jumlah) . "\n";
echo "Saldo setelah OUT: Rp " . number_format($dompet->saldo) . "\n\n";

echo "=== Test Completed Successfully! ===\n";
echo "Total transaksi: " . $user->transaksis->count() . "\n";
