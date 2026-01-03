<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Auto-create default dompets
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

        // Auto-create default kategoris
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
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
