<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FontePagadora;
use App\Models\Obra;
use App\Models\Client;
use App\Models\CategoriaGasto;
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
        $query = Client::query();

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
            ->where('status', true)
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
}
