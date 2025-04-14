<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ObraRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Alterado para true para permitir o acesso
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'endereco' => 'required|string|max:255',
            'data_inicio' => 'required|date',
            'prazo_estimado' => 'required|integer|min:1',
            'area_m2' => 'required|numeric|min:0',
            'valor_estimado' => 'required|numeric|min:0',
            'taxa_administracao' => 'required|numeric|min:0|max:100',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nome.required' => 'O nome da obra é obrigatório',
            'endereco.required' => 'O endereço da obra é obrigatório',
            'data_inicio.required' => 'A data de início é obrigatória',
            'data_inicio.date' => 'A data de início deve ser uma data válida',
            'prazo_estimado.required' => 'O prazo estimado é obrigatório',
            'prazo_estimado.integer' => 'O prazo estimado deve ser um número inteiro',
            'prazo_estimado.min' => 'O prazo estimado deve ser maior que zero',
            'area_m2.required' => 'A área em m² é obrigatória',
            'area_m2.numeric' => 'A área em m² deve ser um número',
            'area_m2.min' => 'A área em m² deve ser maior ou igual a zero',
            'valor_estimado.required' => 'O valor estimado é obrigatório',
            'valor_estimado.numeric' => 'O valor estimado deve ser um número',
            'valor_estimado.min' => 'O valor estimado deve ser maior ou igual a zero',
            'taxa_administracao.required' => 'A taxa de administração é obrigatória',
            'taxa_administracao.numeric' => 'A taxa de administração deve ser um número',
            'taxa_administracao.min' => 'A taxa de administração deve ser maior ou igual a zero',
            'taxa_administracao.max' => 'A taxa de administração deve ser menor ou igual a 100',
        ];
    }
}
