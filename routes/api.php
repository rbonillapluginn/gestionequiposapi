<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotaMovimientoController;
use App\Http\Controllers\Api\ArticuloController;
use App\Http\Controllers\Api\TiendaController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\ProveedorController;
use App\Http\Controllers\Api\PermisoProcesoUsuarioController;
use App\Http\Controllers\Api\ChoferController;
use App\Http\Controllers\Api\VehiculoController;
use App\Http\Controllers\Api\MensajeroController;
use App\Http\Controllers\Api\FirmaDigitalController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas (sin autenticación)
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Rutas protegidas (requieren autenticación)
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::prefix('auth')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
        Route::post('change-password', [AuthController::class, 'changePassword']);
    });

    // Usuarios
    Route::apiResource('users', UserController::class);
    Route::post('users/{id}/reset-password', [UserController::class, 'resetPassword']);

    // Notas de Movimiento
    Route::prefix('notas')->group(function () {
        Route::get('/', [NotaMovimientoController::class, 'index']);
        Route::post('/', [NotaMovimientoController::class, 'store']);
        Route::get('dashboard', [NotaMovimientoController::class, 'dashboard']);
        Route::get('{id}', [NotaMovimientoController::class, 'show']);
        Route::put('{id}', [NotaMovimientoController::class, 'update']);
        Route::delete('{id}', [NotaMovimientoController::class, 'destroy']);
        Route::patch('{id}/status', [NotaMovimientoController::class, 'updateStatus']);
        Route::get('{id}/historial', [NotaMovimientoController::class, 'getHistorial']);
        
        // Firmas digitales
        Route::post('{id}/firmar', [FirmaDigitalController::class, 'firmarYCambiarEstado']);
        Route::get('{id}/firmas', [FirmaDigitalController::class, 'getFirmasPorNota']);
    });
    
    // Firmas digitales
    Route::get('firmas/{id}', [FirmaDigitalController::class, 'show']);

    // Artículos
    Route::apiResource('articulos', ArticuloController::class);
    Route::post('articulos/buscar-codigo', [ArticuloController::class, 'buscarPorCodigo']);

    // Tiendas (solo lectura)
    Route::get('tiendas', [TiendaController::class, 'index']);
    Route::get('tiendas/{id}', [TiendaController::class, 'show']);
    
    // Encargados de tiendas
    Route::get('tiendas/{idTienda}/encargados', [TiendaController::class, 'getEncargados']);
    Route::post('tiendas/{idTienda}/encargados', [TiendaController::class, 'storeEncargado']);
    Route::delete('tiendas/{idTienda}/encargados/{idEncargado}', [TiendaController::class, 'destroyEncargado']);

    // Departamentos
    Route::apiResource('departamentos', DepartamentoController::class);

    // Proveedores
    Route::apiResource('proveedores', ProveedorController::class);

    // Choferes
    Route::apiResource('choferes', ChoferController::class);

    // Vehículos
    Route::apiResource('vehiculos', VehiculoController::class);

    // Mensajeros
    Route::apiResource('mensajeros', MensajeroController::class);

    // Catálogos (datos maestros)
    Route::prefix('catalogs')->group(function () {
        Route::get('/', [CatalogController::class, 'index']);
        
        // Niveles de autorización
        Route::get('niveles-autorizacion', [CatalogController::class, 'nivelesAutorizacion']);
        Route::post('niveles-autorizacion', [CatalogController::class, 'storeNivelAutorizacion']);
        
        // Departamentos
        Route::get('departamentos', [CatalogController::class, 'departamentos']);
        Route::post('departamentos', [CatalogController::class, 'storeDepartamento']);
        
        // Categorías de artículos
        Route::get('categorias-articulos', [CatalogController::class, 'categoriasArticulos']);
        Route::post('categorias-articulos', [CatalogController::class, 'storeCategoriaArticulo']);
        
        // Colores
        Route::get('colores', [CatalogController::class, 'colores']);
        Route::post('colores', [CatalogController::class, 'storeColor']);
        
        // Vehículos
        Route::get('vehiculos', [CatalogController::class, 'vehiculos']);
        Route::post('vehiculos', [CatalogController::class, 'storeVehiculo']);
        
        // Choferes
        Route::get('choferes', [CatalogController::class, 'choferes']);
        Route::post('choferes', [CatalogController::class, 'storeChofer']);
        
        // Mensajeros
        Route::get('mensajeros', [CatalogController::class, 'mensajeros']);
        Route::post('mensajeros', [CatalogController::class, 'storeMensajero']);
        
        // Métodos de envío
        Route::get('metodos-envio', [CatalogController::class, 'metodosEnvio']);
        
        // Tipos de movimiento
        Route::get('tipos-movimiento', [CatalogController::class, 'tiposMovimiento']);
        Route::post('tipos-movimiento', [CatalogController::class, 'storeTipoMovimiento']);
        
        // Estados de nota
        Route::get('estados-nota', [CatalogController::class, 'estadosNota']);
        
        // Unidades de envío
        Route::get('unidades-envio', [CatalogController::class, 'unidadesEnvio']);
    });

    // Permisos
    Route::prefix('permissions')->group(function () {
        Route::get('mis-permisos', [PermissionController::class, 'getMisPermisos']);
        Route::post('verificar-permiso', [PermissionController::class, 'verificarPermiso']);
        
        // Gestión de permisos (solo para administradores)
        Route::get('secciones/{idNivel}', [PermissionController::class, 'getPermisosSecciones']);
        Route::put('secciones/{idNivel}', [PermissionController::class, 'updatePermisosSecciones']);
        Route::get('tipos-movimiento/{idNivel}', [PermissionController::class, 'getPermisosTiposMovimiento']);
        Route::put('tipos-movimiento/{idNivel}', [PermissionController::class, 'updatePermisosTiposMovimiento']);
    });

    // Permisos de Procesos (por usuario)
    Route::prefix('permisos-procesos')->group(function () {
        Route::get('/', [PermisoProcesoUsuarioController::class, 'index']);
        Route::post('/', [PermisoProcesoUsuarioController::class, 'store']);
        Route::delete('{id}', [PermisoProcesoUsuarioController::class, 'destroy']);
        Route::get('usuario/{idUsuario}', [PermisoProcesoUsuarioController::class, 'getPermisosPorUsuario']);
        Route::post('asignar-multiple', [PermisoProcesoUsuarioController::class, 'asignarMultiple']);
        Route::get('verificar', [PermisoProcesoUsuarioController::class, 'verificarPermiso']);
    });

});

// Ruta para verificar el estado de la API
Route::get('health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API funcionando correctamente',
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});