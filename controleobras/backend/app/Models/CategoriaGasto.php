<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaGasto extends Model
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
    ];

    /**
     * Relacionamento muitos para muitos com Obras.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function obras(): BelongsToMany
    {
        return $this->belongsToMany(Obra::class, 'categoria_gasto_obra');
    }
}
