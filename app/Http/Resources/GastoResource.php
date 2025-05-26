<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GastoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'obra_id' => $this->obra_id,
            'categoria_gasto_id' => $this->categoria_gasto_id,
            'fonte_pagadora_id' => $this->fonte_pagadora_id,
            'descricao' => $this->descricao,
            'valor' => (float) $this->valor,
            'data_compra' => $this->data_compra?->format('Y-m-d'),
            'data_pagamento' => $this->data_pagamento?->format('Y-m-d'),
            'numero_documento' => $this->numero_documento,
            'comprovante_url' => $this->comprovante_url,
            'observacoes' => $this->observacoes,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'obra' => $this->whenLoaded('obra', function () {
                return [
                    'id' => $this->obra->id,
                    'nome' => $this->obra->nome,
                ];
            }),
            'categoria_gasto' => $this->whenLoaded('categoriaGasto', function () {
                return [
                    'id' => $this->categoriaGasto->id,
                    'nome' => $this->categoriaGasto->nome,
                ];
            }),
            'fonte_pagadora' => $this->whenLoaded('fontePagadora', function () {
                return [
                    'id' => $this->fontePagadora->id,
                    'nome' => $this->fontePagadora->nome,
                ];
            }),
        ];
    }
}
