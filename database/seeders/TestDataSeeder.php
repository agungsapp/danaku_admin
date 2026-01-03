<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Note: Dompets dan Kategoris default sudah otomatis dibuat oleh UserObserver
        // Seeder ini menambah data testing tambahan
        
        // Tambah dompet tambahan untuk user test (user_id = 2)
        \App\Models\Dompet::create([
            'user_id' => 2,
            'nama' => 'Dana Darurat',
            'saldo' => 5000000,
            'deskripsi' => 'Dana darurat untuk keperluan mendesak'
        ]);

        // Tambah kategori tambahan untuk user test
        \App\Models\Kategori::create([
            'user_id' => 2,
            'nama' => 'Makanan',
            'tipe' => 'out',
        ]);

        \App\Models\Kategori::create([
            'user_id' => 2,
            'nama' => 'Bonus',
            'tipe' => 'in',
        ]);
    }
}
