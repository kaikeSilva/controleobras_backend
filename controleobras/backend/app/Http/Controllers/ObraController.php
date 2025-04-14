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
        if ($request->has('nome')) {
            $query->where('nome', 'like', '%' . $request->nome . '%');
        }
        
        if ($request->has('endereco')) {
            $query->where('endereco', 'like', '%' . $request->endereco . '%');
        }
        
        // Ordenação
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        
        // Validar campos de ordenação permitidos
        $allowedSortFields = ['nome', 'data_inicio', 'prazo_estimado', 'area_m2', 'valor_estimado', 'created_at'];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        // Paginação
        $perPage = $request->get('per_page', 10);
        
        return ObraResource::collection($query->paginate($perPage));
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
