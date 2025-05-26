<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
    ];

    // create atribute nome
    public function getNomeAttribute()
    {
        return $this->name;
    }
}
