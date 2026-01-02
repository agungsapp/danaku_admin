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
        $dompets = [
            ['user_id' => 1, 'nama' => 'Dana Harian', 'saldo' => 1000000],
            ['user_id' => 1, 'nama' => 'Dana Darurat', 'saldo' => 1000000],
            ['user_id' => 1, 'nama' => 'Dana Tabungan', 'saldo' => 1000000],
        ];
        $kategoris = [
            [
                'user_id' => 1,
                'nama' => 'Transportasi',
                'tipe' => 'out',
            ],
            [
                'user_id' => 1,
                'nama' => 'Makanan',
                'tipe' => 'out',
            ],
            [
                'user_id' => 1,
                'nama' => 'Gaji',
                'tipe' => 'in',
            ]
        ];
        foreach ($dompets as $dompet) {
            \App\Models\Dompet::create($dompet);
        }
        foreach ($kategoris as $kategori) {
            \App\Models\Kategori::create($kategori);
        }
    }
}
