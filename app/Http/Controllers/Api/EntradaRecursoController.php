<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EntradaRecursoResource;
use App\Models\EntradaRecurso;
use App\Models\Obra;
use App\Models\FontePagadora;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EntradaRecursoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $perPage = request('per_page', 15);
        $query = EntradaRecurso::with(['obra', 'fontePagadora']);

        // Filtros
        if (request()->has('filter')) {
            $filters = request()->input('filter');
            
            if (isset($filters['search'])) {
                $searchTerm = '%' . $filters['search'] . '%';
                $query->where(function($q) use ($searchTerm) {
                    $q->where('descricao', 'like', $searchTerm)
                      ->orWhereHas('obra', function($q) use ($searchTerm) {
                          $q->where('nome', 'like', $searchTerm);
                      })
                      ->orWhereHas('fontePagadora', function($q) use ($searchTerm) {
                          $q->where('nome', 'like', $searchTerm);
                      })
                      ->orWhere('tipo_entrada', 'like', $searchTerm);
                });
            }

            if (isset($filters['obra_id'])) {
                $query->where('obra_id', $filters['obra_id']);
            }

            if (isset($filters['fonte_pagadora_id'])) {
                $query->where('fonte_pagadora_id', $filters['fonte_pagadora_id']);
            }

            if (isset($filters['tipo_entrada'])) {
                $query->where('tipo_entrada', $filters['tipo_entrada']);
            }

            if (isset($filters['data_entrada'])) {
                $query->whereDate('data_entrada', $filters['data_entrada']);
            }

            if (isset($filters['data_inicio']) && isset($filters['data_fim'])) {
                $query->whereBetween('data_entrada', [
                    $filters['data_inicio'], 
                    $filters['data_fim']
                ]);
            }
        }

        // Ordenação
        $sortField = request('sort', 'data_entrada');
        $sortDirection = request('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        return EntradaRecursoResource::collection($query->paginate($perPage));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'fonte_pagadora_id' => 'required|exists:fonte_pagadoras,id',
            'valor' => 'required|numeric|min:0.01',
            'data_entrada' => 'required|date',
            'descricao' => 'required|string|max:1000',
            'comprovante_url' => 'nullable|url|max:255',
            'tipo_entrada' => 'nullable|in:' . implode(',', array_keys(EntradaRecurso::TIPOS_ENTRADA)),
        ]);

        // Define o tipo_entrada como 'regular' se não for informado
        if (!isset($validated['tipo_entrada'])) {
            $validated['tipo_entrada'] = 'regular';
        }

        $entradaRecurso = EntradaRecurso::create($validated);

        return (new EntradaRecursoResource($entradaRecurso))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $entradaRecurso = EntradaRecurso::with(['obra', 'fontePagadora'])->findOrFail($id);
        return new EntradaRecursoResource($entradaRecurso);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $entradaRecurso = EntradaRecurso::findOrFail($id);

        $validated = $request->validate([
            'obra_id' => 'sometimes|required|exists:obras,id',
            'fonte_pagadora_id' => 'sometimes|required|exists:fonte_pagadoras,id',
            'valor' => 'sometimes|required|numeric|min:0.01',
            'data_entrada' => 'sometimes|required|date',
            'descricao' => 'sometimes|required|string|max:1000',
            'comprovante_url' => 'nullable|url|max:255',
            'tipo_entrada' => 'sometimes|required|in:' . implode(',', array_keys(EntradaRecurso::TIPOS_ENTRADA)),
        ]);

        $entradaRecurso->update($validated);

        return new EntradaRecursoResource($entradaRecurso);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $entradaRecurso = EntradaRecurso::findOrFail($id);
        $entradaRecurso->delete();

        return response()->json([
            'message' => 'Entrada de recurso removida com sucesso.'
        ], Response::HTTP_OK);
    }
}
