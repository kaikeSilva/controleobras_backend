<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class FontePagadora extends Model
{
    /** @use HasFactory<\Database\Factories\FontePagadoraFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
        'data_cadastro',
        'cliente_id',
        'status',
    ];

    protected $casts = [
        'data_cadastro' => 'datetime',
    ];
    
}
