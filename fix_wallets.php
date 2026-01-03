<?php
use App\Models\User;

$users = User::all();
foreach ($users as $user) {
    if ($user->dompets()->count() === 0) {
        $user->dompets()->createMany([
            [
                'nama' => 'Tabungan',
                'saldo' => 0,
                'deskripsi' => 'Dompet tabungan default'
            ],
            [
                'nama' => 'Uang Harian',
                'saldo' => 0,
                'deskripsi' => 'Dompet uang harian default'
            ]
        ]);
        echo "Created defaults for User: {$user->name}\n";
    }
    
    if ($user->kategoris()->count() === 0) {
        $user->kategoris()->createMany([
            [
                'nama' => 'Transportasi',
                'tipe' => 'out'
            ],
            [
                'nama' => 'Gaji',
                'tipe' => 'in'
            ]
        ]);
        echo "Created kategoris for User: {$user->name}\n";
    }
}
echo "Done.\n";
