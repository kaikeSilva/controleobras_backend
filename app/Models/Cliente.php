<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';

    protected $table = 'clientes';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];
    protected $casts = [
        'status' => 'boolean',
    ];

    public function categoriaGastos()
    {
        return $this->hasMany(CategoriaGasto::class);
    }
    
    public function getNomeAttribute()
    {
        return $this->name;
    }
}
