<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password'];

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}
