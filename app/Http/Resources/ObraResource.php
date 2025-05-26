<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\AutocompleteResource;

class ObraResource extends JsonResource
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
            'cliente_id' => $this->cliente_id,
            'cliente' => new AutocompleteResource($this->whenLoaded('cliente')),
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'endereco' => $this->endereco,
            'area_m2' => $this->area_m2,
            'data_inicio' => optional($this->data_inicio)?->toDateString(),
            'prazo_estimado' => optional($this->prazo_estimado)?->toDateString(),
            'valor_estimado' => $this->valor_estimado,
            'taxa_administracao' => $this->taxa_administracao,
            'status' => $this->status,
            'ativo' => $this->ativo,
            'created_at' => optional($this->created_at)?->toISOString(),
            'updated_at' => optional($this->updated_at)?->toISOString(),
            'deleted_at' => optional($this->deleted_at)?->toISOString(),
        ];
    }
}
