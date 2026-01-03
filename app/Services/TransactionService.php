<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaksi;
use App\Models\Dompet;
use App\Models\Kategori;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionService
{
    /**
     * Create a new transaction and update wallet balance
     *
     * @param User $user
     * @param array $data
     * @return Transaksi
     * @throws ValidationException
     */
    public function createTransaction(User $user, array $data): Transaksi
    {
        return DB::transaction(function () use ($user, $data) {
            // Validate dompet belongs to user
            $dompet = Dompet::where('id', $data['dompet_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Validate kategori belongs to user
            $kategori = Kategori::where('id', $data['kategori_id'])
                ->where('user_id', $user->id)
                ->firstOrFail();

            // Create transaction
            $transaksi = Transaksi::create([
                'user_id' => $user->id,
                'dompet_id' => $dompet->id,
                'kategori_id' => $kategori->id,
                'judul' => $data['judul'],
                'jumlah' => $data['jumlah'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            // Update wallet balance based on category type
            if ($kategori->tipe === 'in') {
                $dompet->saldo += $data['jumlah'];
            } else {
                $dompet->saldo -= $data['jumlah'];
            }
            $dompet->save();

            return $transaksi->load(['dompet', 'kategori']);
        });
    }

    /**
     * Update transaction and recalculate wallet balance
     *
     * @param Transaksi $transaksi
     * @param array $data
     * @return Transaksi
     */
    public function updateTransaction(Transaksi $transaksi, array $data): Transaksi
    {
        return DB::transaction(function () use ($transaksi, $data) {
            // Rollback old transaction effect
            $originalDompetId = $transaksi->getOriginal('dompet_id');
            $originalKategoriId = $transaksi->getOriginal('kategori_id');
            $originalJumlah = $transaksi->getOriginal('jumlah');

            $oldDompet = Dompet::find($originalDompetId);
            $oldKategori = Kategori::find($originalKategoriId);
            
            if ($oldDompet && $oldKategori) {
                if ($oldKategori->tipe === 'in') {
                    $oldDompet->saldo -= $originalJumlah;
                } else {
                    $oldDompet->saldo += $originalJumlah;
                }
                $oldDompet->save();
            }

            // Validate new dompet belongs to user
            $newDompet = Dompet::where('id', $data['dompet_id'])
                ->where('user_id', $transaksi->user_id)
                ->firstOrFail();

            // Validate new kategori belongs to user
            $newKategori = Kategori::where('id', $data['kategori_id'])
                ->where('user_id', $transaksi->user_id)
                ->firstOrFail();

            // Update transaction
            $transaksi->update([
                'dompet_id' => $newDompet->id,
                'kategori_id' => $newKategori->id,
                'judul' => $data['judul'],
                'jumlah' => $data['jumlah'],
                'deskripsi' => $data['deskripsi'] ?? null,
            ]);

            // Apply new transaction effect
            if ($newKategori->tipe === 'in') {
                $newDompet->saldo += $data['jumlah'];
            } else {
                $newDompet->saldo -= $data['jumlah'];
            }
            $newDompet->save();

            return $transaksi->fresh()->load(['dompet', 'kategori']);
        });
    }

    /**
     * Delete transaction and rollback wallet balance
     *
     * @param Transaksi $transaksi
     * @return bool
     */
    public function deleteTransaction(Transaksi $transaksi): bool
    {
        return DB::transaction(function () use ($transaksi) {
            $dompet = $transaksi->dompet;
            $kategori = $transaksi->kategori;

            // Rollback transaction effect
            if ($kategori->tipe === 'in') {
                $dompet->saldo -= $transaksi->jumlah;
            } else {
                $dompet->saldo += $transaksi->jumlah;
            }
            $dompet->save();

            return $transaksi->delete();
        });
    }
}
