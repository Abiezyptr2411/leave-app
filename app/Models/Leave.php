<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'user_id', 'jenis_cuti', 'tanggal_mulai', 'tanggal_selesai', 'alasan', 'status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

