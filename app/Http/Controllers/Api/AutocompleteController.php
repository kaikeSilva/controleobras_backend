<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FontePagadora;
use App\Models\Obra;
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
}
