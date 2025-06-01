<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GastoResource;
use App\Models\Gasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GastoController extends Controller
{
    public function index(Request $request)
    {
        // frontend can sent filter with the values or only the keys at the request
        if ($request->has('filter')) {
            $filters = $request->input('filter');
        } else {
            $filters = $request->all();
        }
        
        $query = Gasto::with(['obra', 'categoriaGasto', 'fontePagadora']);

        // Filtros
        if ($filters) {
            $search = $filters['search'] ?? null;

            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('descricao', 'like', "%{$search}%")
                      ->orWhere('numero_documento', 'like', "%{$search}%")
                      ->orWhere('valor', 'like', "%{$search}%")
                      ->orWhere('data_compra', 'like', "%{$search}%")
                      ->orWhere('data_pagamento', 'like', "%{$search}%")
                      ->orWhereHas('obra', function($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%");
                      })
                      ->orWhereHas('categoriaGasto', function($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%");
                      })
                      ->orWhereHas('fontePagadora', function($q) use ($search) {
                          $q->where('nome', 'like', "%{$search}%");
                      });
                });
            }

            // Filtro por obra_id
            if (!empty($filters['obra_id'])) {
                $query->where('obra_id', $filters['obra_id']);
            }

            // Filtro por obras
            if (!empty($filters['obras'])) {
                $query->whereIn('obra_id', $filters['obras']);
            }

            // Filtro por categoria_gasto_id
            if (!empty($filters['categoria_gasto_id'])) {
                $query->where('categoria_gasto_id', $filters['categoria_gasto_id']);
            }

            $categorias = $filters['categorias_gasto'] ?? [];
            if (!empty($categorias)) {
                $query->whereIn('categoria_gasto_id', $categorias);
            }

            // Filtro por fonte_pagadora_id
            if (!empty($filters['fonte_pagadora_id'])) {
                $query->where('fonte_pagadora_id', $filters['fonte_pagadora_id']);
            }

            // Filtro por data_compra
            if (!empty($filters['data_compra'])) {
                $query->whereDate('data_compra', $filters['data_compra']);
            }

            // Filtro por data_pagamento
            if (isset($filters['data_pagamento'])) {
                if ($filters['data_pagamento'] === 'null') {
                    $query->whereNull('data_pagamento');
                } else {
                    $query->whereDate('data_pagamento', $filters['data_pagamento']);
                }
            }

            // Filtro por valor mínimo
            if (!empty($filters['valor_min'])) {
                $query->where('valor', '>=', $filters['valor_min']);
            }

            // Filtro por valor máximo
            if (!empty($filters['valor_max'])) {
                $query->where('valor', '<=', $filters['valor_max']);
            }

            if (!empty($filters['data_inicio']) && !empty($filters['data_fim'])) {
                $dataInicio = Carbon::parse($filters['data_inicio'])->startOfDay();
                $dataFim = Carbon::parse($filters['data_fim'])->endOfDay();
                $query->whereNotNull('data_pagamento')
                      ->whereBetween('data_pagamento', [$dataInicio, $dataFim]);
            }
        }

        // Ordenação
        $sortField = $request->input('sort', 'data_compra');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Paginação
        if ($request->has('per_page') && $request->input('per_page') == 'all') {
            return GastoResource::collection($query->get());
        }

        $perPage = $request->input('per_page', 15);
        return GastoResource::collection($query->paginate($perPage));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'obra_id' => 'required|exists:obras,id',
            'categoria_gasto_id' => 'required|exists:categoria_gastos,id',
            'fonte_pagadora_id' => 'required|exists:fonte_pagadoras,id',
            'descricao' => 'required|string|max:1000',
            'valor' => 'required|numeric|min:0.01',
            'data_compra' => 'required|date',
            'data_pagamento' => 'nullable|date|after_or_equal:data_compra',
            'numero_documento' => 'nullable|string|max:100',
            'comprovante_url' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string|max:2000',
        ], [
            'obra_id.required' => 'O campo obra é obrigatório.',
            'obra_id.exists' => 'A obra selecionada é inválida.',
            'categoria_gasto_id.required' => 'O campo categoria de gasto é obrigatório.',
            'categoria_gasto_id.exists' => 'A categoria de gasto selecionada é inválida.',
            'fonte_pagadora_id.required' => 'O campo fonte pagadora é obrigatório.',
            'fonte_pagadora_id.exists' => 'A fonte pagadora selecionada é inválida.',
            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'valor.required' => 'O campo valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'data_compra.required' => 'O campo data da compra é obrigatório.',
            'data_compra.date' => 'A data da compra deve ser uma data válida.',
            'data_pagamento.date' => 'A data do pagamento deve ser uma data válida.',
            'data_pagamento.after_or_equal' => 'A data do pagamento deve ser igual ou posterior à data da compra.',
            'numero_documento.max' => 'O número do documento não pode ter mais de 100 caracteres.',
            'comprovante_url.url' => 'O campo comprovante deve ser uma URL válida.',
            'comprovante_url.max' => 'A URL do comprovante não pode ter mais de 255 caracteres.',
            'observacoes.max' => 'As observações não podem ter mais de 2000 caracteres.',
        ]);

        $gasto = Gasto::create($validated);
        return (new GastoResource($gasto->load(['obra', 'categoriaGasto', 'fontePagadora'])))
            ->response()
            ->setStatusCode(201);
    }

    public function show($id)
    {
        $gasto = Gasto::with(['obra', 'categoriaGasto', 'fontePagadora'])->find($id);
        
        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto não encontrado.',
                'errors' => [
                    'id' => ['O gasto com o ID informado não foi encontrado.']
                ]
            ], 404);
        }
        
        return new GastoResource($gasto);
    }

    public function update(Request $request, $id)
    {
        $gasto = Gasto::find($id);
        
        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto não encontrado.',
                'errors' => [
                    'id' => ['O gasto com o ID informado não foi encontrado.']
                ]
            ], 404);
        }

        $validated = $request->validate([
            'obra_id' => 'sometimes|required|exists:obras,id',
            'categoria_gasto_id' => 'sometimes|required|exists:categoria_gastos,id',
            'fonte_pagadora_id' => 'sometimes|required|exists:fonte_pagadoras,id',
            'descricao' => 'sometimes|required|string|max:1000',
            'valor' => 'sometimes|required|numeric|min:0.01',
            'data_compra' => 'sometimes|required|date',
            'data_pagamento' => 'nullable|date|after_or_equal:data_compra',
            'numero_documento' => 'nullable|string|max:100',
            'comprovante_url' => 'nullable|url|max:255',
            'observacoes' => 'nullable|string|max:2000',
        ], [
            'obra_id.required' => 'O campo obra é obrigatório.',
            'obra_id.exists' => 'A obra selecionada é inválida.',
            'categoria_gasto_id.required' => 'O campo categoria de gasto é obrigatório.',
            'categoria_gasto_id.exists' => 'A categoria de gasto selecionada é inválida.',
            'fonte_pagadora_id.required' => 'O campo fonte pagadora é obrigatório.',
            'fonte_pagadora_id.exists' => 'A fonte pagadora selecionada é inválida.',
            'descricao.required' => 'O campo descrição é obrigatório.',
            'descricao.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'valor.required' => 'O campo valor é obrigatório.',
            'valor.numeric' => 'O valor deve ser um número.',
            'valor.min' => 'O valor deve ser maior que zero.',
            'data_compra.required' => 'O campo data da compra é obrigatório.',
            'data_compra.date' => 'A data da compra deve ser uma data válida.',
            'data_pagamento.date' => 'A data do pagamento deve ser uma data válida.',
            'data_pagamento.after_or_equal' => 'A data do pagamento deve ser igual ou posterior à data da compra.',
            'numero_documento.max' => 'O número do documento não pode ter mais de 100 caracteres.',
            'comprovante_url.url' => 'O campo comprovante deve ser uma URL válida.',
            'comprovante_url.max' => 'A URL do comprovante não pode ter mais de 255 caracteres.',
            'observacoes.max' => 'As observações não podem ter mais de 2000 caracteres.',
        ]);

        $gasto->update($validated);
        return new GastoResource($gasto->load(['obra', 'categoriaGasto', 'fontePagadora']));
    }

    public function destroy($id)
    {
        $gasto = Gasto::withTrashed()->find($id);
        
        if (!$gasto) {
            return response()->json([
                'message' => 'Gasto não encontrado.',
                'errors' => [
                    'id' => ['O gasto com o ID informado não foi encontrado.']
                ]
            ], 404);
        }

        if ($gasto->trashed()) {
            return response()->json([
                'message' => 'Este gasto já foi excluído.',
                'errors' => [
                    'id' => ['Este gasto já foi excluído anteriormente.']
                ]
            ], 404);
        }

        $gasto->delete();
        return response()->json([
            'message' => 'Gasto excluído com sucesso.'
        ]);
    }
}
