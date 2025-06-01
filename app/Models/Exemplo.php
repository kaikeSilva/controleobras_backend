<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exemplo extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS_ATIVO = 'ativo';
    const STATUS_INATIVO = 'inativo';

    protected $fillable = [
        'nome',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];
}
