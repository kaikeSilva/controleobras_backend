<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EntradaRecurso extends Model
{
    use SoftDeletes,HasFactory;

    /**
     * Tipos de entrada de recurso disponíveis.
     *
     * @var array
     */
    const TIPOS_ENTRADA = [
        'aporte_inicial' => 'Aporte Inicial',
        'aporte_adicional' => 'Aporte Adicional',
        'reembolso' => 'Reembolso',
        'regular' => 'Regular',
    ];

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'obra_id',
        'fonte_pagadora_id',
        'valor',
        'data_entrada',
        'descricao',
        'comprovante_url',
        'tipo_entrada',
    ];

    /**
     * Os atributos que devem ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'data_entrada' => 'date',
        'valor' => 'decimal:2',
    ];

    public function getNomeAttribute()
    {
        return "{$this->obra->nome} - {$this->fontePagadora->nome} - {$this->tipo_entrada_formatado} - {$this->valor_formatado}";
    }

    /**
     * Obtém a obra relacionada ao recurso.
     */
    public function obra(): BelongsTo
    {
        return $this->belongsTo(Obra::class);
    }

    /**
     * Obtém a fonte pagadora relacionada ao recurso.
     */
    public function fontePagadora(): BelongsTo
    {
        return $this->belongsTo(FontePagadora::class);
    }

    /**
     * Obtém o tipo de entrada formatado.
     *
     * @return string
     */
    public function getTipoEntradaFormatadoAttribute(): string
    {
        return self::TIPOS_ENTRADA[$this->tipo_entrada] ?? 'Desconhecido';
    }

    public function getValorFormatadoAttribute(): string
    {
        return 'R$ ' . number_format($this->valor, 2, ',', '.');
    }
}
