<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    protected $table = 'cuti';
    protected $fillable = ['user_id', 'alasan', 'tanggal_mulai', 'tanggal_selesai', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

