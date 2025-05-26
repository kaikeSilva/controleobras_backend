<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaGastoResource;
use App\Models\CategoriaGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoriaGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CategoriaGasto::with('cliente');

        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }


            // Filtro por cliente_id
            if (!empty($filters['cliente_id'])) {
                $query->where('cliente_id', $filters['cliente_id']);
            }

            // Filtro por status
            if (isset($filters['status']) && $filters['status'] !== '') {
                $query->where('status', (bool)$filters['status']);
            }

            // Filtro geral 'busca' (LIKE em campos específicos)
            $stringFields = ['nome', 'descricao', 'cor'];
            if (!empty($filters['busca'])) {
                $busca = $filters['busca'];
                $query->where(function($q) use ($busca, $stringFields) {
                    foreach ($stringFields as $field) {
                        $q->orWhere($field, 'LIKE', '%' . $busca . '%');
                    }
                });
            }

            // Filtros por string (LIKE) individuais
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
            }
        }


        // Ordenação
        $sortBy = $request->input('sort_by', 'nome');
        $direction = $request->input('direction', 'asc');
        $allowedSortFields = ['id', 'nome', 'status', 'cliente_id', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }

        // Paginação
        $perPage = (int) $request->input('per_page', 15);
        $categorias = $query->paginate($perPage);

        return CategoriaGastoResource::collection($categorias);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255|unique:categoria_gastos,nome',
            'status' => 'sometimes|boolean',
            'cliente_id' => 'nullable|exists:clients,id',
            'descricao' => 'nullable|string',
            'cor' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        ]);

        // Gera o slug a partir do nome
        $validated['slug'] = Str::slug($validated['nome']);

        // Verifica se já existe uma categoria com o mesmo slug para o mesmo cliente
        $existing = CategoriaGasto::where('slug', $validated['slug'])
            ->where('cliente_id', $validated['cliente_id'] ?? null)
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Já existe uma categoria com este nome para o cliente selecionado.',
                'errors' => [
                    'nome' => ['Já existe uma categoria com este nome para o cliente selecionado.']
                ]
            ], 422);
        }

        $categoria = CategoriaGasto::create($validated);
        return (new CategoriaGastoResource($categoria->load('cliente')))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaGasto $categoriaGasto)
    {
        return new CategoriaGastoResource($categoriaGasto->load('cliente'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CategoriaGasto $categoriaGasto)
    {
        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255|unique:categoria_gastos,nome,' . $categoriaGasto->id,
            'status' => 'sometimes|boolean',
            'cliente_id' => 'nullable|exists:clients,id',
            'descricao' => 'nullable|string',
            'cor' => 'nullable|string|regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/',
        ]);

        // Se o nome foi alterado, atualiza o slug
        if (isset($validated['nome']) && $validated['nome'] !== $categoriaGasto->nome) {
            $validated['slug'] = Str::slug($validated['nome']);
            
            // Verifica se já existe outra categoria com o mesmo slug para o mesmo cliente
            $existing = CategoriaGasto::where('slug', $validated['slug'])
                ->where('id', '!=', $categoriaGasto->id)
                ->where('cliente_id', $validated['cliente_id'] ?? $categoriaGasto->cliente_id)
                ->exists();

            if ($existing) {
                return response()->json([
                    'message' => 'Já existe uma categoria com este nome para o cliente selecionado.',
                    'errors' => [
                        'nome' => ['Já existe uma categoria com este nome para o cliente selecionado.']
                    ]
                ], 422);
            }
        }

        $categoriaGasto->update($validated);
        return new CategoriaGastoResource($categoriaGasto->load('cliente'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CategoriaGasto $categoriaGasto)
    {
        $categoriaGasto->delete();
        return response()->json(null, 204);
    }
}
