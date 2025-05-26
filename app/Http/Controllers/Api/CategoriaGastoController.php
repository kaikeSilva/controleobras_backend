<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriaGastoResource;
use App\Models\CategoriaGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
                $query->where('status', $filters['status']);
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
            'status' => [
                'required', 
                'string', 
                Rule::in([CategoriaGasto::STATUS_ATIVO, CategoriaGasto::STATUS_INATIVO])
            ],
            'cliente_id' => 'nullable|exists:clientes,id',
            'descricao' => 'nullable|string',
            'cor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'nome.unique' => 'Já existe uma categoria com este nome.',
            'status.required' => 'O campo status é obrigatório.',
            'status.string' => 'O status deve ser um texto.',
            'status.in' => 'O status deve ser "ativo" ou "inativo".',
            'cliente_id.exists' => 'O cliente selecionado não foi encontrado.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'cor.regex' => 'A cor deve estar no formato hexadecimal (ex: #FFFFFF ou #FFF).',
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
    public function show($id)
    {
        $categoria = CategoriaGasto::with('cliente')->find($id);
        
        if (!$categoria) {
            return response()->json([
                'message' => 'Categoria de gasto não encontrada.',
                'errors' => [
                    'id' => ['A categoria de gasto com o ID informado não foi encontrada.']
                ]
            ], 404);
        }
        
        return new CategoriaGastoResource($categoria);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $categoria = CategoriaGasto::with('cliente')->find($id);
        
        if (!$categoria) {
            return response()->json([
                'message' => 'Categoria de gasto não encontrada.',
                'errors' => [
                    'id' => ['A categoria de gasto com o ID informado não foi encontrada.']
                ]
            ], 404);
        }

        $validated = $request->validate([
            'nome' => 'sometimes|required|string|max:255|unique:categoria_gastos,nome,' . $id,
            'status' => [
                'sometimes', 
                'required', 
                'string', 
                Rule::in([CategoriaGasto::STATUS_ATIVO, CategoriaGasto::STATUS_INATIVO])
            ],
            'cliente_id' => 'nullable|exists:clientes,id',
            'descricao' => 'nullable|string',
            'cor' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
        ], [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.string' => 'O nome deve ser um texto.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'nome.unique' => 'Já existe uma categoria com este nome.',
            'status.required' => 'O campo status é obrigatório.',
            'status.string' => 'O status deve ser um texto.',
            'status.in' => 'O status deve ser "ativo" ou "inativo".',
            'cliente_id.exists' => 'O cliente selecionado não foi encontrado.',
            'descricao.string' => 'A descrição deve ser um texto.',
            'cor.regex' => 'A cor deve estar no formato hexadecimal (ex: #FFFFFF ou #FFF).',
        ]);

        // Se o nome foi alterado, atualiza o slug
        if (isset($validated['nome']) && $validated['nome'] !== $categoria->nome) {
            $validated['slug'] = Str::slug($validated['nome']);
            
            // Verifica se já existe outra categoria com o mesmo slug para o mesmo cliente
            $existing = CategoriaGasto::where('slug', $validated['slug'])
                ->where('id', '!=', $id)
                ->where('cliente_id', $validated['cliente_id'] ?? $categoria->cliente_id)
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

        $categoria->update($validated);
        return new CategoriaGastoResource($categoria);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $categoria = CategoriaGasto::withTrashed()->find($id);
        
        if (!$categoria) {
            return response()->json([
                'message' => 'Categoria de gasto não encontrada.',
                'errors' => [
                    'id' => ['A categoria de gasto com o ID informado não foi encontrada.']
                ]
            ], 404);
        }
        
        if ($categoria->trashed()) {
            return response()->json([
                'message' => 'Categoria de gasto não encontrada.',
                'errors' => [
                    'id' => ['Esta categoria de gasto já foi excluída.']
                ]
            ], 404);
        }
        
        $categoria->delete();
        return response()->json(null, 204);
    }
}
