<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FontePagadora;
use App\Models\Obra;
use App\Models\Cliente;
use App\Models\CategoriaGasto;
use App\Models\Gasto;
use App\Models\EntradaRecurso;
use App\Http\Resources\AutocompleteResource;

class AutocompleteController extends Controller
{
    //
    /**
     * Retorna lista de fontes pagadoras ativas para autocomplete/select.
     */
    public function fontesPagadoras()
    {
        $query = FontePagadora::query();
        $query->where('ativo', true);

        if (request()->has('cliente_id')) {
            $query->where('cliente_id', request('cliente_id'));
        }

        $fontes = $query->orderBy('nome')->get();
        return AutocompleteResource::collection($fontes);
    }

    /**
     * Retorna lista de gastos para autocomplete/select, com filtros opcionais.
     */
    public function gastos()
    {
        $query = Gasto::query()
            ->select(['id', 'descricao', 'valor', 'data_compra', 'obra_id', 'categoria_gasto_id', 'fonte_pagadora_id'])
            ->with(['obra:id,nome', 'categoriaGasto:id,nome', 'fontePagadora:id,nome']);

        // Filtros
        if (request()->has('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('numero_documento', 'like', "%{$search}%")
                  ->orWhereHas('obra', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  })
                  ->orWhereHas('categoriaGasto', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fontePagadora', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por obra_id
        if (request()->has('obra_id')) {
            $query->where('obra_id', request('obra_id'));
        }

        // Filtro por categoria_gasto_id
        if (request()->has('categoria_gasto_id')) {
            $query->where('categoria_gasto_id', request('categoria_gasto_id'));
        }

        // Filtro por fonte_pagadora_id
        if (request()->has('fonte_pagadora_id')) {
            $query->where('fonte_pagadora_id', request('fonte_pagadora_id'));
        }

        // Limita o número de resultados para evitar sobrecarga
        $gastos = $query->orderByDesc('data_compra')->take(50)->get();

        return AutocompleteResource::collection($gastos);
    }

    /**
     * Retorna lista de obras ativas para autocomplete/select, filtrando por cliente_id.
     */
    public function obras()
    {
        $query = Obra::query();
        $query->where('ativo', true);
        if (request()->has('cliente_id')) {
            $query->where('cliente_id', request('cliente_id'));
        }
        $obras = $query->orderBy('nome')->get();
        return AutocompleteResource::collection($obras);
    }

    /**
     * Retorna lista de clientes para autocomplete/select.
     */
    public function clientes()
    {
        $query = Cliente::query();

        if (request()->has('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        $clientes = $query->orderBy('name')->get();
        return AutocompleteResource::collection($clientes);
    }

    /**
     * Retorna lista de categorias de gastos para autocomplete/select.
     */
    public function categoriasGastos()
    {
        $query = CategoriaGasto::query()
            ->where('status', CategoriaGasto::STATUS_ATIVO)
            ->orderBy('nome');

        if (request()->has('cliente_id')) {
            $query->where(function($q) {
                $q->where('cliente_id', request('cliente_id'))
                  ->orWhereNull('cliente_id');
            });
        }

        if (request()->has('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('descricao', 'LIKE', "%{$search}%");
            });
        }

        $categorias = $query->orderBy('nome')->get();
        return AutocompleteResource::collection($categorias);
    }

    /**
     * Retorna lista de entradas de recursos para autocomplete/select, com filtros opcionais.
     */
    public function entradasRecursos()
    {
        $query = EntradaRecurso::query()
            ->select(['id', 'descricao', 'valor', 'data_entrada', 'obra_id', 'fonte_pagadora_id', 'tipo_entrada'])
            ->with(['obra:id,nome', 'fontePagadora:id,nome']);

        // Filtros
        if (request()->has('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('descricao', 'like', "%{$search}%")
                  ->orWhere('tipo_entrada', 'like', "%{$search}%")
                  ->orWhereHas('obra', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  })
                  ->orWhereHas('fontePagadora', function($q) use ($search) {
                      $q->where('nome', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por obra_id
        if (request()->has('obra_id')) {
            $query->where('obra_id', request('obra_id'));
        }

        // Filtro por fonte_pagadora_id
        if (request()->has('fonte_pagadora_id')) {
            $query->where('fonte_pagadora_id', request('fonte_pagadora_id'));
        }

        // Filtro por tipo_entrada
        if (request()->has('tipo_entrada')) {
            $query->where('tipo_entrada', request('tipo_entrada'));
        }

        // Ordenação
        $query->orderBy('data_entrada', 'desc')
              ->orderBy('id', 'desc');

        // Formata os resultados para o formato esperado pelo select2 (se estiver usando)
        $results = $query->get();
        return AutocompleteResource::collection($results);
    }
}
