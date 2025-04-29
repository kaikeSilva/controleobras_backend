<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoriaGastoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Alterado para true para permitir a requisição
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string',
            'obras' => 'sometimes|array',
            'obras.*' => 'exists:obras,id',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.max' => 'O nome da categoria não pode ter mais de 255 caracteres.',
            'obras.*.exists' => 'Uma ou mais obras selecionadas não existem.',
        ];
    }
}
