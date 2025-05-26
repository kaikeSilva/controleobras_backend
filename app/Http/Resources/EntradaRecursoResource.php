<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntradaRecursoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'obra_id' => $this->obra_id,
            'obra' => $this->whenLoaded('obra', function () {
                return [
                    'id' => $this->obra->id,
                    'nome' => $this->obra->nome,
                ];
            }),
            'fonte_pagadora_id' => $this->fonte_pagadora_id,
            'fonte_pagadora' => $this->whenLoaded('fontePagadora', function () {
                return [
                    'id' => $this->fontePagadora->id,
                    'nome' => $this->fontePagadora->nome,
                ];
            }),
            'valor' => (float) $this->valor,
            'valor_formatado' => 'R$ ' . number_format($this->valor, 2, ',', '.'),
            'data_entrada' => $this->data_entrada->format('Y-m-d'),
            'data_entrada_formatada' => $this->data_entrada->format('d/m/Y'),
            'descricao' => $this->descricao,
            'comprovante_url' => $this->comprovante_url,
            'tipo_entrada' => $this->tipo_entrada,
            'tipo_entrada_formatado' => $this->tipo_entrada_formatado,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'created_at_formatado' => $this->created_at->format('d/m/Y H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'updated_at_formatado' => $this->updated_at->format('d/m/Y H:i:s'),
            'deleted_at' => $this->whenNotNull($this->deleted_at?->format('Y-m-d H:i:s')),
            'deleted_at_formatado' => $this->whenNotNull($this->deleted_at?->format('d/m/Y H:i:s')),
        ];
    }
}
