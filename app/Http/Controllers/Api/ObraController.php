<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Obra;
use App\Http\Resources\ObraResource;
use Illuminate\Support\Facades\Validator;

class ObraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Obra::query();
        // Filtros
        if (request()->has('filter')) {
            $filters = request()->input('filter');
            if (!empty($filters['cliente_id'])) {
                $query->where('cliente_id', $filters['cliente_id']);
            }
            if (!empty($filters['search'])) {
                $query->where(function($q) use ($filters) {
                    $q->where('nome', 'LIKE', "%{$filters['search']}%")
                      ->orWhere('descricao', 'LIKE', "%{$filters['search']}%")
                      ->orWhere('status', 'LIKE', "%{$filters['search']}%") ;
                });
            }
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['ativo'])) {
                $query->where('ativo', $filters['ativo']);
            }
        }
        $perPage = request('per_page', 15);
        $orderBy = request('sort', 'created_at');
        $orderDirection = request('order', 'desc');
        $obras = $query->orderBy($orderBy, $orderDirection)->paginate($perPage);
        return ObraResource::collection($obras);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clients,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'nullable|string|max:255',
            'area_m2' => 'nullable|numeric',
            'data_inicio' => 'nullable|date',
            'prazo_estimado' => 'nullable|date',
            'valor_estimado' => 'nullable|numeric',
            'taxa_administracao' => 'nullable|numeric',
            'status' => 'string',
            'ativo' => 'boolean',
        ]);
        
        $obra = Obra::create($validated);
        return new ObraResource($obra);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $obra = Obra::findOrFail($id);
        return new ObraResource($obra);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $obra = Obra::findOrFail($id);
        $validated = $request->validate([
            'cliente_id' => 'sometimes|exists:clients,id',
            'nome' => 'sometimes|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'nullable|string|max:255',
            'area_m2' => 'nullable|numeric',
            'data_inicio' => 'nullable|date',
            'prazo_estimado' => 'nullable|date',
            'valor_estimado' => 'nullable|numeric',
            'taxa_administracao' => 'nullable|numeric',
            'status' => 'sometimes|in:em_andamento,concluida,pausada',
            'ativo' => 'boolean',
        ]);
        $obra->update($validated);
        return new ObraResource($obra);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $obra = Obra::findOrFail($id);
        $obra->delete();
        return response()->json(['message' => 'Obra removida com sucesso.']);
    }
}
