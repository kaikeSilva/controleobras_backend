<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\FontePagadora;
use App\Http\Resources\FontePagadoraResource;

class FontePagadoraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FontePagadora::query();

        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Filtro por cliente
            if (!empty($filters['cliente_id'])) {
                $query->where('cliente_id', $filters['cliente_id']);
            }

            // Busca geral por nome, descricao, status
            $stringFields = ['nome', 'descricao', 'status'];
            if (!empty($filters['busca'])) {
                $query->where(function($q) use ($filters, $stringFields) {
                    foreach ($stringFields as $field) {
                        $q->orWhere($field, 'LIKE', '%' . $filters['busca'] . '%');
                    }
                });
            }

            // Filtros individuais
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }

            // Filtro por status booleano (ativo)
            if (isset($filters['ativo'])) {
                $query->where('ativo', $filters['ativo']);
            }
        }

        // Ordenação
        $sortBy = $request->input('sort_by', 'id');
        $direction = $request->input('direction', 'asc');
        $allowedSortFields = ['id', 'nome', 'descricao', 'status', 'ativo', 'data_cadastro', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }

        $perPage = $request->input('per_page', 15);
        $results = $query->paginate($perPage);
        return FontePagadoraResource::collection($results);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
            'data_cadastro' => 'nullable|date',
            'cliente_id' => 'required|exists:clients,id',
            'status' => 'required|in:ativo,inativo',
        ]);
        $fonte = FontePagadora::create($validated);
        return new FontePagadoraResource($fonte);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $fonte = FontePagadora::findOrFail($id);
        return new FontePagadoraResource($fonte);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $fonte = FontePagadora::findOrFail($id);
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'descricao' => 'nullable|string',
            'ativo' => 'boolean',
            'data_cadastro' => 'nullable|date',
            'cliente_id' => 'sometimes|required|exists:clients,id',
            'status' => 'required|in:ativo,inativo',
        ]);
        $fonte->update($validated);
        return new FontePagadoraResource($fonte);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fonte = FontePagadora::findOrFail($id);
        $fonte->delete();
        return response()->json(['message' => 'Fonte Pagadora removida com sucesso.']);
    }
}
