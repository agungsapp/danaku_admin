<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $fillable = [
        'user_id',
        'dompet_id',
        'kategori_id',
        'judul',
        'jumlah',
        'deskripsi',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function dompet()
    {
        return $this->belongsTo(Dompet::class);
    }
    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }
}
