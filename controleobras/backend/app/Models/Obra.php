<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Obra extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nome',
        'descricao',
        'endereco',
        'data_inicio',
        'prazo_estimado',
        'area_m2',
        'valor_estimado',
        'taxa_administracao',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'data_inicio' => 'date',
        'prazo_estimado' => 'integer',
        'area_m2' => 'decimal:2',
        'valor_estimado' => 'decimal:2',
        'taxa_administracao' => 'decimal:2',
    ];

    /**
     * Relacionamento muitos para muitos com Categorias de Gasto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function categoriasGasto(): BelongsToMany
    {
        return $this->belongsToMany(CategoriaGasto::class, 'categoria_gasto_obra');
    }
}
