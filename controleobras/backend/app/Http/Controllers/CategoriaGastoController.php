<?php

namespace App\Http\Controllers;

use App\Models\CategoriaGasto;
use App\Http\Requests\CategoriaGastoRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoriaGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\ResourceCollection
     */
    public function index(Request $request)
    {
        $query = CategoriaGasto::query();

        // Filtrar por nome
        if ($request->has('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }

        // Ordenação
        $sortBy = $request->input('sort_by', 'nome');
        $sortDirection = $request->input('sort_direction', 'asc');
        $query->orderBy($sortBy, $sortDirection);

        // Paginação
        $perPage = $request->input('per_page', 15);
        $categorias = $query->paginate($perPage);

        return new ResourceCollection($categorias);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CategoriaGastoRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CategoriaGastoRequest $request)
    {
        $categoriaGasto = CategoriaGasto::create($request->validated());

        // Sincronizar obras relacionadas, se houver
        if ($request->has('obras')) {
            $categoriaGasto->obras()->sync($request->obras);
        }

        return response()->json([
            'message' => 'Categoria de gasto criada com sucesso.',
            'data' => new JsonResource($categoriaGasto),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CategoriaGasto  $categoriaGasto
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(CategoriaGasto $categoriaGasto)
    {
        // Carregar relacionamento com obras
        $categoriaGasto->load('obras');

        return response()->json([
            'data' => new JsonResource($categoriaGasto),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CategoriaGastoRequest  $request
     * @param  \App\Models\CategoriaGasto  $categoriaGasto
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CategoriaGastoRequest $request, CategoriaGasto $categoriaGasto)
    {
        $categoriaGasto->update($request->validated());

        // Sincronizar obras relacionadas, se houver
        if ($request->has('obras')) {
            $categoriaGasto->obras()->sync($request->obras);
        }

        return response()->json([
            'message' => 'Categoria de gasto atualizada com sucesso.',
            'data' => new JsonResource($categoriaGasto),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CategoriaGasto  $categoriaGasto
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(CategoriaGasto $categoriaGasto)
    {
        // Remover relacionamentos com obras
        $categoriaGasto->obras()->detach();
        
        // Excluir a categoria
        $categoriaGasto->delete();

        return response()->json([
            'message' => 'Categoria de gasto excluída com sucesso.',
        ]);
    }
}
