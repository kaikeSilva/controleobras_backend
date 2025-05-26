<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Http\Resources\ClientResource;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Cliente::query();

        // Filtros
        if ($request->has('filter')) {
            $filters = $request->input('filter');

            // Filtro por ID (exato)
            if (!empty($filters['id'])) {
                $query->where('id', $filters['id']);
            }

            // Filtro geral 'busca' (LIKE em todos os campos string)
            $stringFields = ['name', 'email', 'phone', 'address'];
            if (!empty($filters['busca'])) {
                $query->where(function($q) use ($filters, $stringFields) {
                    foreach ($stringFields as $field) {
                        $q->orWhere($field, 'LIKE', '%' . $filters['busca'] . '%');
                    }
                });
            }

            // Filtros por string (LIKE) individuais
            foreach ($stringFields as $field) {
                if (!empty($filters[$field])) {
                    $query->where($field, 'LIKE', '%' . $filters[$field] . '%');
                }
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
        $allowedSortFields = ['id', 'name', 'email', 'phone', 'address', 'created_at', 'updated_at'];
        $allowedDirections = ['asc', 'desc'];
        if (in_array($sortBy, $allowedSortFields) && in_array($direction, $allowedDirections)) {
            $query->orderBy($sortBy, $direction);
        }

        // Paginação
        $perPage = (int) $request->input('per_page', 15);
        $clientes = $query->paginate($perPage);

        return ClientResource::collection($clientes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clientes,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        $client = Cliente::create($validated);
        return (new ClientResource($client))->response()->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Cliente $client)
    {
        return new ClientResource($client);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cliente $client)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:clientes,email,' . $client->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);
        $client->update($validated);
        return new ClientResource($client);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cliente $client)
    {
        $client->delete();
        return response()->json(null, 204);
    }
}
