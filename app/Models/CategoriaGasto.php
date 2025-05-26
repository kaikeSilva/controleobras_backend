<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class CategoriaGasto extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categoria_gastos';

    protected $fillable = [
        'nome',
        'slug',
        'status',
        'cliente_id',
        'descricao',
        'cor'
    ];

    protected $casts = [
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->nome);
            }
        });

        static::updating(function ($model) {
            if ($model->isDirty('nome') && empty($model->slug)) {
                $model->slug = Str::slug($model->nome);
            }
        });
    }

    // Relacionamentos
    public function cliente()
    {
        return $this->belongsTo(Client::class, 'cliente_id');
    }

    // Escopos
    public function scopeAtivo($query)
    {
        return $query->where('status', true);
    }

    public function scopeDoCliente($query, $clienteId = null)
    {
        if ($clienteId) {
            return $query->where('cliente_id', $clienteId);
        }
        return $query->whereNull('cliente_id');
    }

    // Acessores
    public function getNomeFormatadoAttribute()
    {
        return $this->nome;
    }

    // Métodos estáticos para validação
    public static function rules($id = null)
    {
        $uniqueRule = 'unique:categoria_gastos';
        if ($id) {
            $uniqueRule .= ",$id,id";
        }

        return [
            'nome' => ['required', 'string', 'max:255', $uniqueRule],
            'slug' => ['required', 'string', 'max:255', $uniqueRule],
            'status' => ['boolean'],
            'cliente_id' => ['nullable', 'exists:clients,id'],
            'descricao' => ['nullable', 'string'],
            'cor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ];
    }
}
