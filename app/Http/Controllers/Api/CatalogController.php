<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NivelAutorizacion;
use App\Models\Seccion;
use App\Models\TipoMovimiento;
use App\Models\Departamento;
use App\Models\CategoriaArticulo;
use App\Models\Color;
use App\Models\TipoUnidadEnvio;
use App\Models\TipoMaterial;
use App\Models\MetodoEnvio;
use App\Models\SubmetodoEnvio;
use App\Models\EstadoNota;
use App\Models\Vehiculo;
use App\Models\Chofer;
use App\Models\Mensajero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CatalogController extends Controller
{
    /**
     * Obtener todos los catálogos
     */
    public function index()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'niveles_autorizacion' => NivelAutorizacion::where('activo', true)->orderBy('orden_jerarquico')->get(),
                'secciones' => Seccion::where('activo', true)->get(),
                'tipos_movimiento' => TipoMovimiento::where('activo', true)->get(),
                'departamentos' => Departamento::where('activo', true)->get(),
                'categorias_articulos' => CategoriaArticulo::where('activo', true)->get(),
                'colores' => Color::where('activo', true)->get(),
                'tipos_unidad_envio' => TipoUnidadEnvio::where('activo', true)->get(),
                'tipos_material' => TipoMaterial::where('activo', true)->get(),
                'metodos_envio' => MetodoEnvio::where('activo', true)->with('submetodos')->get(),
                'estados_nota' => EstadoNota::orderBy('orden')->get(),
                'vehiculos' => Vehiculo::where('activo', true)->get(),
                'choferes' => Chofer::where('activo', true)->get(),
                'mensajeros' => Mensajero::where('activo', true)->get(),
            ]
        ]);
    }

    // NIVELES DE AUTORIZACIÓN
    public function nivelesAutorizacion()
    {
        $niveles = NivelAutorizacion::where('activo', true)->orderBy('orden_jerarquico')->get();
        
        return response()->json([
            'success' => true,
            'data' => $niveles
        ]);
    }

    public function storeNivelAutorizacion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_nivel' => 'required|string|max:50|unique:niveles_autorizacion',
            'descripcion' => 'nullable|string',
            'orden_jerarquico' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $nivel = NivelAutorizacion::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Nivel de autorización creado exitosamente',
            'data' => $nivel
        ], 201);
    }

    // DEPARTAMENTOS
    public function departamentos()
    {
        $departamentos = Departamento::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $departamentos
        ]);
    }

    public function storeDepartamento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_departamento' => 'required|string|max:100',
            'codigo_departamento' => 'required|string|max:20|unique:departamentos',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $departamento = Departamento::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Departamento creado exitosamente',
            'data' => $departamento
        ], 201);
    }

    // CATEGORÍAS DE ARTÍCULOS
    public function categoriasArticulos()
    {
        $categorias = CategoriaArticulo::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $categorias
        ]);
    }

    public function storeCategoriaArticulo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_categoria' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $categoria = CategoriaArticulo::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Categoría creada exitosamente',
            'data' => $categoria
        ], 201);
    }

    // COLORES
    public function colores()
    {
        $colores = Color::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $colores
        ]);
    }

    public function storeColor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_color' => 'required|string|max:50|unique:colores',
            'codigo_hex' => 'nullable|string|max:7',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $color = Color::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Color creado exitosamente',
            'data' => $color
        ], 201);
    }

    // VEHÍCULOS
    public function vehiculos()
    {
        $vehiculos = Vehiculo::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $vehiculos
        ]);
    }

    public function storeVehiculo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'numero_camion' => 'required|string|max:50|unique:vehiculos',
            'placa' => 'nullable|string|max:20',
            'modelo' => 'nullable|string|max:100',
            'capacidad_carga' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $vehiculo = Vehiculo::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Vehículo creado exitosamente',
            'data' => $vehiculo
        ], 201);
    }

    // CHOFERES
    public function choferes()
    {
        $choferes = Chofer::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $choferes
        ]);
    }

    public function storeChofer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'licencia' => 'required|string|max:50|unique:choferes',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $chofer = Chofer::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Chofer creado exitosamente',
            'data' => $chofer
        ], 201);
    }

    // MENSAJEROS
    public function mensajeros()
    {
        $mensajeros = Mensajero::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $mensajeros
        ]);
    }

    public function storeMensajero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required|string|max:200',
            'identificacion' => 'required|string|max:50|unique:mensajeros',
            'telefono' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $mensajero = Mensajero::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Mensajero creado exitosamente',
            'data' => $mensajero
        ], 201);
    }

    // MÉTODOS DE ENVÍO
    public function metodosEnvio()
    {
        $metodos = MetodoEnvio::where('activo', true)->with('submetodos')->get();
        
        return response()->json([
            'success' => true,
            'data' => $metodos
        ]);
    }

    // TIPOS DE MOVIMIENTO
    public function tiposMovimiento()
    {
        $tipos = TipoMovimiento::where('activo', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $tipos
        ]);
    }

    public function storeTipoMovimiento(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_tipo' => 'required|string|max:50|unique:tipos_movimiento',
            'codigo_tipo' => 'required|string|max:20|unique:tipos_movimiento',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $tipo = TipoMovimiento::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Tipo de movimiento creado exitosamente',
            'data' => $tipo
        ], 201);
    }

    // ESTADOS DE NOTA
    public function estadosNota()
    {
        $estados = EstadoNota::orderBy('orden')->get();
        
        return response()->json([
            'success' => true,
            'data' => $estados
        ]);
    }

    // UNIDADES DE ENVÍO
    public function unidadesEnvio()
    {
        $unidades = \App\Models\UnidadEnvio::with(['tipoUnidad', 'tipoMaterial', 'color'])
            ->where('activo', true)
            ->get();
        
        return response()->json([
            'success' => true,
            'data' => $unidades
        ]);
    }
}