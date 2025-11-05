<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticuloRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nombre_articulo' => 'required|string|max:200',
            'descripcion' => 'nullable|string',
            'id_categoria' => 'nullable|exists:categorias_articulos,id_categoria',
            'codigo_barra' => 'nullable|string|max:100|unique:articulos',
            'numero_serie' => 'nullable|string|max:100|unique:articulos',
            'precio' => 'nullable|numeric|min:0',
            'activo' => 'boolean',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->codigo_barra && !$this->numero_serie) {
                $validator->errors()->add('identificacion', 'Debe proporcionar código de barra o número de serie');
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'nombre_articulo.required' => 'El nombre del artículo es obligatorio',
            'nombre_articulo.max' => 'El nombre del artículo no puede exceder 200 caracteres',
            'codigo_barra.unique' => 'Este código de barra ya está registrado',
            'numero_serie.unique' => 'Este número de serie ya está registrado',
            'precio.numeric' => 'El precio debe ser un valor numérico',
            'precio.min' => 'El precio no puede ser negativo',
            'id_categoria.exists' => 'La categoría seleccionada no existe',
        ];
    }
}