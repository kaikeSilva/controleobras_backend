<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FontePagadora;
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
}
