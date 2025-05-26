<?php

namespace App\Models;

use App\Models\Cliente;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;

class Obra extends Model
{
    use HasFactory, SoftDeletes;

    const STATUS = [
        'em_andamento' => 'Em Andamento',
        'concluida' => 'Concluida',
        'pausada' => 'Pausada',
    ];

    protected $fillable = [
        'cliente_id',
        'nome',
        'descricao',
        'endereco',
        'area_m2',
        'data_inicio',
        'prazo_estimado',
        'valor_estimado',
        'taxa_administracao',
        'status',
        'ativo',
    ];

    protected $casts = [
        'data_inicio' => 'date',
        'prazo_estimado' => 'date',
        'area_m2' => 'decimal:2',
        'valor_estimado' => 'decimal:2',
        'taxa_administracao' => 'decimal:2',
        'ativo' => 'boolean',
        'status' => 'string',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }
}
