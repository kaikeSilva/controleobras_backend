<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Exemplo;
use App\Http\Resources\ExemploResource;
use Illuminate\Support\Str;

class ExemploController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Exemplo::query();

        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }

            // Filtro por nome (LIKE)
            if (!empty($filters['nome'])) {
                $query->where('nome', 'LIKE', '%' . $filters['nome'] . '%');
            }

            // Filtro por status (exato)
            if (isset($filters['status']) && $filters['status'] !== '') {
                $query->where('status', $filters['status']);
            }

            // Filtro geral 'busca' (LIKE em nome)
            if (!empty($filters['busca'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('nome', 'LIKE', '%' . $filters['busca'] . '%');
                });
            }

            // Filtros por data
            $dateFields = ['created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (!empty($filters[$field])) {
                    $query->whereDate($field, $filters[$field]);
                }
            }
        }

        // Ordenação
        $sortBy = $request->input('sort_by', 'id');
        $direction = $request->input('direction', 'asc');
        $allowedSortFields = ['id', 'nome', 'status', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }

        // Paginação
        if ($request->has('per_page') && $request->input('per_page') == 'all') {
            return ExemploResource::collection($query->get());
        }
        
        $perPage = (int) $request->input('per_page', 15);
        $exemplos = $query->paginate($perPage);

        return ExemploResource::collection($exemplos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'status' => 'nullable|in:ativo,inativo',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'status.in' => 'O status deve ser ativo ou inativo.',
        ]);

        // Verifica se já existe um exemplo com o mesmo nome
        if (Exemplo::where('nome', $validated['nome'])->exists()) {
            return response()->json([
                'message' => 'Já existe um exemplo com este nome.',
                'errors' => [
                    'nome' => ['Já existe um exemplo com este nome.']
                ]
            ], 422);
        }

        // Define o status como ativo por padrão, se não for fornecido
        if (!isset($validated['status'])) {
            $validated['status'] = 'ativo';
        }

        $exemplo = Exemplo::create($validated);
        return (new ExemploResource($exemplo))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $exemplo = Exemplo::findOrFail($id);
        return new ExemploResource($exemplo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $exemplo = Exemplo::findOrFail($id);

        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255',
            'status' => 'nullable|in:ativo,inativo',
        ], [
            'nome.required' => 'O nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'status.in' => 'O status deve ser ativo ou inativo.',
        ]);

        // Verifica se já existe outro exemplo com o mesmo nome
        if (isset($validated['nome']) && 
            Exemplo::where('nome', $validated['nome'])
                ->where('id', '!=', $id)
                ->exists()) {
            return response()->json([
                'message' => 'Já existe um exemplo com este nome.',
                'errors' => [
                    'nome' => ['Já existe um exemplo com este nome.']
                ]
            ], 422);
        }

        $exemplo->update($validated);
        return new ExemploResource($exemplo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $exemplo = Exemplo::findOrFail($id);
        $exemplo->delete();
        return response()->json(null, 204);
    }
}
