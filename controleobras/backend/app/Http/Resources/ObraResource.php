<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ObraResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nome' => $this->nome,
            'descricao' => $this->descricao,
            'endereco' => $this->endereco,
            'data_inicio' => $this->data_inicio->format('Y-m-d'),
            'prazo_estimado' => $this->prazo_estimado,
            'area_m2' => (float) $this->area_m2,
            'valor_estimado' => (float) $this->valor_estimado,
            'taxa_administracao' => (float) $this->taxa_administracao,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Customize the pagination information for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Http\Resources\Json\AnonymousResourceCollection  $resource
     * @return array
     */
    public static function paginationInformation($request, $paginated, $default)
    {
        return $default;
    }
}
