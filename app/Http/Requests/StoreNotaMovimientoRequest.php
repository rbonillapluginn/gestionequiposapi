<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreNotaMovimientoRequest extends FormRequest
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
            'tipo_nota' => 'required|in:ENTRADA,SALIDA',
            'id_tipo_movimiento' => 'required|exists:tipos_movimiento,id_tipo_movimiento',
            'id_tienda_origen' => 'nullable|exists:tiendas,id_tienda',
            'id_tienda_destino' => 'nullable|exists:tiendas,id_tienda',
            'proveedor_origen' => 'nullable|string|max:200',
            'proveedor_destino' => 'nullable|string|max:200',
            'id_metodo_envio' => 'required|exists:metodos_envio,id_metodo_envio',
            'id_submetodo_envio' => 'nullable|exists:submetodos_envio,id_submetodo',
            'id_vehiculo' => 'nullable|exists:vehiculos,id_vehiculo',
            'id_chofer' => 'nullable|exists:choferes,id_chofer',
            'hora_salida' => 'nullable|date',
            'id_mensajero' => 'nullable|exists:mensajeros,id_mensajero',
            'observaciones' => 'nullable|string',
            'articulos' => 'required|array|min:1',
            'articulos.*.id_articulo' => 'required|exists:articulos,id_articulo',
            'articulos.*.cantidad' => 'required|integer|min:1',
            'articulos.*.id_unidad_envio' => 'nullable|exists:unidades_envio,id_unidad_envio',
            'articulos.*.observaciones' => 'nullable|string',
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
            $tieneOrigen = $this->id_tienda_origen || $this->proveedor_origen;
            $tieneDestino = $this->id_tienda_destino || $this->proveedor_destino;

            if (!$tieneOrigen) {
                $validator->errors()->add('origen', 'Debe especificar una tienda de origen o proveedor de origen');
            }

            if (!$tieneDestino) {
                $validator->errors()->add('destino', 'Debe especificar una tienda de destino o proveedor de destino');
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
            'tipo_nota.required' => 'El tipo de nota es obligatorio',
            'tipo_nota.in' => 'El tipo de nota debe ser ENTRADA o SALIDA',
            'id_tipo_movimiento.required' => 'El tipo de movimiento es obligatorio',
            'id_tipo_movimiento.exists' => 'El tipo de movimiento seleccionado no existe',
            'id_metodo_envio.required' => 'El método de envío es obligatorio',
            'id_metodo_envio.exists' => 'El método de envío seleccionado no existe',
            'articulos.required' => 'Debe incluir al menos un artículo',
            'articulos.min' => 'Debe incluir al menos un artículo',
            'articulos.*.id_articulo.required' => 'El ID del artículo es obligatorio',
            'articulos.*.id_articulo.exists' => 'El artículo seleccionado no existe',
            'articulos.*.cantidad.required' => 'La cantidad es obligatoria',
            'articulos.*.cantidad.integer' => 'La cantidad debe ser un número entero',
            'articulos.*.cantidad.min' => 'La cantidad debe ser mayor a 0',
        ];
    }
}