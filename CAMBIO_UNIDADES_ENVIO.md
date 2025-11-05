# CAMBIO: Creación Dinámica de Unidades de Envío

## Resumen del Cambio

**ANTES:** La tabla `unidades_envio` se llenaba anticipadamente con combinaciones pre-configuradas de tipos, materiales y colores.

**AHORA:** La tabla `unidades_envio` se llena **dinámicamente** cuando el usuario crea una nota de movimiento, ya que es en ese momento cuando decide cómo enviar los artículos.

---

## Impacto en el Sistema

### 1. Endpoint Modificado

**POST /api/notas** (Crear Nota de Movimiento)

#### Payload ANTERIOR:
```json
{
  "articulos": [
    {
      "id_articulo": 1,
      "cantidad": 5,
      "id_unidad_envio": 12,  // ← Se enviaba ID pre-existente
      "observaciones": "..."
    }
  ]
}
```

#### Payload NUEVO:
```json
{
  "articulos": [
    {
      "id_articulo": 1,
      "cantidad": 5,
      "unidad_envio": {  // ← Ahora se envían los datos para crear la unidad
        "id_tipo_unidad": 1,        // Caja/Sobre/Bulto
        "id_tipo_material": 2,      // Cartón/Plástico/etc (solo si es Caja)
        "id_color": 5,              // Solo si material es Plástico
        "tiene_cintillo": true,     // Solo si material es Plástico
        "dimensiones": "30x40x50",  // Opcional
        "peso_maximo": 25.5,        // Opcional
        "descripcion": "Caja mediana de plástico rojo"
      },
      "observaciones": "..."
    }
  ]
}
```

---

## Validaciones Actualizadas

### Campos Requeridos por Artículo:
```
articulos.*.id_articulo                        - requerido
articulos.*.cantidad                           - requerido
articulos.*.unidad_envio.id_tipo_unidad       - requerido
articulos.*.unidad_envio.id_tipo_material     - nullable
articulos.*.unidad_envio.id_color             - nullable
articulos.*.unidad_envio.tiene_cintillo       - boolean (default: false)
articulos.*.unidad_envio.dimensiones          - nullable, max 100 chars
articulos.*.unidad_envio.peso_maximo          - nullable, numeric
articulos.*.unidad_envio.descripcion          - nullable
articulos.*.observaciones                      - nullable
```

### Reglas de Negocio:
1. **Solo Cajas tienen material:** Si `id_tipo_unidad` != "Caja", entonces `id_tipo_material` debe ser `null`
2. **Solo Plástico tiene color/cintillo:** Si `id_tipo_material` != "Plástico", entonces `id_color` y `tiene_cintillo` deben ser `null`/`false`

---

## Lógica del Controlador

### NotaMovimientoController::store()

```php
foreach ($request->articulos as $articulo) {
    $idUnidadEnvio = null;
    
    // Crear unidad de envío dinámicamente si se especificaron los datos
    if (isset($articulo['unidad_envio'])) {
        $unidadEnvioData = $articulo['unidad_envio'];
        
        // Crear la unidad de envío
        $unidadEnvio = \App\Models\UnidadEnvio::create([
            'id_tipo_unidad' => $unidadEnvioData['id_tipo_unidad'],
            'id_tipo_material' => $unidadEnvioData['id_tipo_material'] ?? null,
            'id_color' => $unidadEnvioData['id_color'] ?? null,
            'tiene_cintillo' => $unidadEnvioData['tiene_cintillo'] ?? false,
            'dimensiones' => $unidadEnvioData['dimensiones'] ?? null,
            'peso_maximo' => $unidadEnvioData['peso_maximo'] ?? null,
            'descripcion' => $unidadEnvioData['descripcion'] ?? null,
            'activo' => true,
        ]);
        
        $idUnidadEnvio = $unidadEnvio->id_unidad_envio;
    }
    
    // Crear detalle de artículo con la unidad recién creada
    DetalleNotaArticulo::create([
        'id_nota' => $nota->id_nota,
        'id_articulo' => $articulo['id_articulo'],
        'cantidad' => $articulo['cantidad'],
        'id_unidad_envio' => $idUnidadEnvio,
        'observaciones' => $articulo['observaciones'] ?? null,
    ]);
}
```

---

## Impacto en el Frontend

### Lo que CAMBIA:
1. **Formulario de Nueva Nota:** Agregar sección dinámica para configurar unidad de envío por artículo
2. **Eliminar selector pre-configurado:** Ya no se selecciona de un catálogo existente
3. **Nuevos campos por artículo:**
   - Tipo de Unidad (select: Caja/Sobre/Bulto)
   - Tipo de Material (select condicional si es Caja)
   - Color (select condicional si es Plástico)
   - Tiene Cintillo (checkbox condicional si es Plástico)
   - Dimensiones (input opcional)
   - Peso Máximo (input opcional)
   - Descripción (textarea opcional)

### Lo que NO CAMBIA:
- Listado de notas existentes (sigue mostrando unidades de envío asociadas)
- Detalle de nota (sigue mostrando tipo, material, color, etc.)
- Endpoints de consulta (GET /api/notas, GET /api/notas/{id})

---

## Endpoints de Catálogos (para llenar selects)

```
GET /api/catalogs                    - Todos los catálogos
GET /api/catalogs/colores            - Lista de colores
```

**Respuesta de /api/catalogs:**
```json
{
  "success": true,
  "data": {
    "tipos_unidad_envio": [
      { "id_tipo_unidad": 1, "nombre_tipo": "Caja" },
      { "id_tipo_unidad": 2, "nombre_tipo": "Sobre" },
      { "id_tipo_unidad": 3, "nombre_tipo": "Bulto" }
    ],
    "tipos_material": [
      { "id_tipo_material": 1, "nombre_material": "Cartón", "requiere_color": false },
      { "id_tipo_material": 2, "nombre_material": "Plástico", "requiere_color": true, "requiere_cintillo": true },
      { "id_tipo_material": 3, "nombre_material": "Madera", "requiere_color": false },
      { "id_tipo_material": 4, "nombre_material": "Metal", "requiere_color": false }
    ],
    "colores": [
      { "id_color": 1, "nombre_color": "Rojo", "codigo_hex": "#FF0000" },
      { "id_color": 2, "nombre_color": "Azul", "codigo_hex": "#0000FF" }
    ]
  }
}
```

---

## Ejemplo Completo: Crear Nota con 2 Artículos

```json
{
  "tipo_nota": "SALIDA",
  "id_tipo_movimiento": 1,
  "id_tienda_origen": 5,
  "id_tienda_destino": 8,
  "id_metodo_envio": 1,
  "id_vehiculo": 3,
  "id_chofer": 2,
  "hora_salida": "2024-11-03 14:00:00",
  "observaciones": "Envío urgente",
  "articulos": [
    {
      "id_articulo": 101,
      "cantidad": 3,
      "unidad_envio": {
        "id_tipo_unidad": 1,          // Caja
        "id_tipo_material": 2,        // Plástico
        "id_color": 5,                // Rojo
        "tiene_cintillo": true,
        "dimensiones": "30x40x50",
        "peso_maximo": 25.5,
        "descripcion": "Caja mediana de plástico rojo con cintillo"
      },
      "observaciones": "Frágil"
    },
    {
      "id_articulo": 102,
      "cantidad": 10,
      "unidad_envio": {
        "id_tipo_unidad": 2,          // Sobre
        "id_tipo_material": null,
        "id_color": null,
        "tiene_cintillo": false,
        "dimensiones": "A4",
        "peso_maximo": 0.5,
        "descripcion": "Sobre tamaño A4"
      }
    }
  ]
}
```

---

## Ventajas del Nuevo Enfoque

1. **Flexibilidad:** El usuario decide en el momento cómo empacar cada artículo
2. **Sin pre-configuración:** No necesitas anticipar todas las combinaciones posibles
3. **Trazabilidad:** Cada nota registra exactamente cómo se empacó
4. **Historial:** Puedes analizar qué tipos de empaque se usan más

---

## Consideraciones Importantes

### Base de Datos
- La tabla `unidades_envio` crecerá con cada nota creada
- Considera agregar índices si el volumen es alto
- Puedes implementar limpieza de unidades antiguas o no usadas

### Performance
- Cada artículo genera 1 INSERT en `unidades_envio`
- Usa transacciones (ya implementado con `DB::beginTransaction()`)
- El rollback automático revierte unidades creadas si falla la nota

### Validación
- El backend valida que los IDs de tipo/material/color existan
- El frontend debe validar reglas de negocio antes de enviar
- Mostrar/ocultar campos según selección (UX)

---

## Prompt Actualizado para el Frontend

Ver archivo: `PROMPT_FRONTEND_NUEVA_NOTA_ACTUALIZADO.md`

---

## Archivos Modificados

1. `app/Http/Controllers/Api/NotaMovimientoController.php`
   - Cambio en validaciones del método `store()`
   - Lógica para crear `UnidadEnvio` dinámicamente

2. Archivos sin cambios (ya estaban preparados):
   - `app/Models/UnidadEnvio.php` (fillable correcto)
   - `database/migrations/2024_01_01_000004_create_unidades_envio_tables.php`
   - `app/Models/DetalleNotaArticulo.php`

---

## Testing Recomendado

### Casos de Prueba:
1. Crear nota con artículo en **Caja de Cartón** (sin color ni cintillo)
2. Crear nota con artículo en **Caja de Plástico Rojo con cintillo**
3. Crear nota con artículo en **Sobre** (sin material)
4. Crear nota con **múltiples artículos** cada uno con diferente tipo de empaque
5. Validar que rollback funciona si falla alguna validación

---

## Fecha de Implementación
3 de noviembre de 2025
