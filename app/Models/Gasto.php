<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gasto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'obra_id',
        'categoria_gasto_id',
        'fonte_pagadora_id',
        'descricao',
        'valor',
        'data_compra',
        'data_pagamento',
        'numero_documento',
        'comprovante_url',
        'observacoes',
    ];

    protected $casts = [
        'data_compra' => 'date:Y-m-d',
        'data_pagamento' => 'date:Y-m-d',
        'valor' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    public function categoriaGasto(): BelongsTo
    {
        return $this->belongsTo(CategoriaGasto::class);
    }

    public function fontePagadora(): BelongsTo
    {
        return $this->belongsTo(FontePagadora::class);
    }
}
