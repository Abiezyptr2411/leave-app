<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UploadedDocument extends Model
{
    use HasFactory;

    protected $fillable = ['filename', 'filepath'];

    // public function cuti()
    // {
    //     return $this->belongsTo(Cuti::class);
    // }
}
