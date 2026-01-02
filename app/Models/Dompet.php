<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dompet extends Model
{
    protected $fillable = ['user_id', 'nama', 'saldo', 'deskripsi'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
