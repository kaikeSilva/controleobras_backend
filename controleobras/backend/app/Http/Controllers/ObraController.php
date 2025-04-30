<?php

namespace App\Http\Controllers;

use App\Models\Obra;
use App\Http\Requests\ObraRequest;
use App\Http\Resources\ObraResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ObraController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $query = Obra::query();
        
        // Aplicar filtros
        $this->applyFilters($query, $request);
        
        // Ordenação
        $allowedSortFields = ['nome', 'endereco', 'data_inicio', 'prazo_estimado', 'area_m2', 'valor_estimado', 'taxa_administracao', 'created_at'];
        $sortBy = in_array($request->sort_by, $allowedSortFields) ? $request->sort_by : 'created_at';
        $sortDirection = $request->sort_direction === 'desc' ? 'desc' : 'asc';
        
        $query->orderBy($sortBy, $sortDirection);
        
        // Paginação
        $perPage = $request->per_page ?? 10;
        
        return ObraResource::collection($query->paginate($perPage));
    }
    
    /**
     * Retorna os acumuladores de valor estimado e lucro estimado para todas as obras com os filtros aplicados
     */
    public function getTotals(Request $request)
    {
        $query = Obra::query();
        
        // Aplicar os mesmos filtros usados na listagem
        $this->applyFilters($query, $request);
        
        // Calcular os totais
        $totals = [
            'valor_estimado_total' => $query->sum('valor_estimado'),
            'lucro_estimado_total' => $query->sum(\DB::raw('valor_estimado * (taxa_administracao / 100)'))
        ];
        
        return response()->json($totals);
    }
    
    /**
     * Aplica os filtros à query
     */
    private function applyFilters($query, $request)
    {
        // Filtros
        if ($request->filled('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        
        if ($request->filled('endereco')) {
            $query->where('endereco', 'like', '%' . $request->endereco . '%');
        }
        
        // Filtro de data de início com intervalo
        if ($request->filled('data_inicio_min')) {
            $query->where('data_inicio', '>=', $request->data_inicio_min);
        }
        
        if ($request->filled('data_inicio_max')) {
            $query->where('data_inicio', '<=', $request->data_inicio_max);
        }
        
        // Filtros de prazo estimado
        if ($request->filled('prazo_estimado_min')) {
            $query->where('prazo_estimado', '>=', $request->prazo_estimado_min);
        }
        
        if ($request->filled('prazo_estimado_max')) {
            $query->where('prazo_estimado', '<=', $request->prazo_estimado_max);
        }
        
        // Filtros de valor estimado
        if ($request->filled('valor_estimado_min')) {
            $query->where('valor_estimado', '>=', $request->valor_estimado_min);
        }
        
        if ($request->filled('valor_estimado_max')) {
            $query->where('valor_estimado', '<=', $request->valor_estimado_max);
        }
        
        // Filtros de taxa de administração
        if ($request->filled('taxa_administracao_min')) {
            $query->where('taxa_administracao', '>=', $request->taxa_administracao_min);
        }
        
        if ($request->filled('taxa_administracao_max')) {
            $query->where('taxa_administracao', '<=', $request->taxa_administracao_max);
        }
        
        // Filtros de área
        if ($request->filled('area_m2_min')) {
            $query->where('area_m2', '>=', $request->area_m2_min);
        }
        
        if ($request->filled('area_m2_max')) {
            $query->where('area_m2', '<=', $request->area_m2_max);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ObraRequest  $request
     * @return \App\Http\Resources\ObraResource
     */
    public function store(ObraRequest $request)
    {
        $obra = Obra::create($request->validated());
        
        return new ObraResource($obra);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Obra  $obra
     * @return \App\Http\Resources\ObraResource
     */
    public function show(Obra $obra)
    {
        return new ObraResource($obra);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ObraRequest  $request
     * @param  \App\Models\Obra  $obra
     * @return \App\Http\Resources\ObraResource
     */
    public function update(ObraRequest $request, Obra $obra)
    {
        $obra->update($request->validated());
        
        return new ObraResource($obra);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Obra  $obra
     * @return \Illuminate\Http\Response
     */
    public function destroy(Obra $obra)
    {
        $obra->delete();
        
        return response()->noContent();
    }
}
