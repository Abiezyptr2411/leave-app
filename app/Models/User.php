<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'nik',
        'name',
        'email',
        'password',
        'role',
        'photo',      
        'division',   
    ];

    protected $hidden = ['password'];

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}
