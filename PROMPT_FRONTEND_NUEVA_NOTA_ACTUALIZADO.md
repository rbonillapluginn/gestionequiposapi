# PROMPT: Actualización de Nueva Nota - Creación Dinámica de Unidades de Envío

## CAMBIO IMPORTANTE

**La lógica de unidades de envío ha cambiado completamente.**

**ANTES:** El usuario seleccionaba de un catálogo pre-configurado de unidades de envío.

**AHORA:** El usuario configura cómo empacar cada artículo **en el momento de crear la nota**.

---

## Endpoint de Creación

**POST /api/notas**

Headers:
```
Authorization: Bearer <token>
Content-Type: application/json
```

---

## Estructura del Payload

### Payload Completo Ejemplo:

```json
{
  "tipo_nota": "SALIDA",
  "id_tipo_movimiento": 1,
  "id_tienda_origen": 5,
  "id_tienda_destino": 8,
  "proveedor_origen": null,
  "proveedor_destino": null,
  "id_metodo_envio": 1,
  "id_submetodo_envio": 2,
  "id_vehiculo": 3,
  "id_chofer": 2,
  "hora_salida": "2024-11-03 14:00:00",
  "id_mensajero": null,
  "observaciones": "Envío urgente para el viernes",
  "articulos": [
    {
      "id_articulo": 101,
      "cantidad": 3,
      "unidad_envio": {
        "id_tipo_unidad": 1,
        "id_tipo_material": 2,
        "id_color": 5,
        "tiene_cintillo": true,
        "dimensiones": "30x40x50",
        "peso_maximo": 25.5,
        "descripcion": "Caja mediana de plástico rojo con cintillo"
      },
      "observaciones": "Artículos frágiles"
    },
    {
      "id_articulo": 102,
      "cantidad": 10,
      "unidad_envio": {
        "id_tipo_unidad": 2,
        "id_tipo_material": null,
        "id_color": null,
        "tiene_cintillo": false,
        "dimensiones": "A4",
        "peso_maximo": 0.5,
        "descripcion": "Sobre tamaño carta"
      },
      "observaciones": null
    }
  ]
}
```

---

## Campos del Formulario

### Sección: Información General de la Nota
- Tipo de Nota (radio: ENTRADA/SALIDA)
- Tipo de Movimiento (select)
- Tienda Origen (select o input proveedor)
- Tienda Destino (select o input proveedor)
- Método de Envío (select)
- Submétodo de Envío (select condicional)
- Vehículo (select opcional)
- Chofer (select opcional)
- Hora de Salida (datetime opcional)
- Mensajero (select opcional)
- Observaciones generales (textarea)

### Sección: Artículos y Empaque (REPETIBLE)

**Por cada artículo:**

1. **Artículo** (autocomplete/select)
   - Buscar por código o nombre
   - Mostrar: código, nombre, categoría

2. **Cantidad** (number input, min: 1)

3. **Configuración de Empaque** (campos dinámicos):

   a) **Tipo de Unidad** (select, requerido)
      - Opciones: Caja, Sobre, Bulto
      - Evento onChange: mostrar/ocultar campos siguientes

   b) **Tipo de Material** (select)
      - **Visible solo si:** Tipo de Unidad = "Caja"
      - Opciones: Cartón, Plástico, Madera, Metal
      - Evento onChange: mostrar/ocultar color y cintillo

   c) **Color** (select)
      - **Visible solo si:** Tipo de Material = "Plástico"
      - Cargar desde /api/catalogs

   d) **Requiere Cintillo** (checkbox)
      - **Visible solo si:** Tipo de Material = "Plástico"
      - Default: false

   e) **Dimensiones** (input text, opcional)
      - Placeholder: "Ej: 30x40x50 cm"
      - Max 100 caracteres

   f) **Peso Máximo** (number input, opcional)
      - Placeholder: "Ej: 25.5"
      - Unidad: kg
      - Permitir decimales

   g) **Descripción del Empaque** (textarea, opcional)
      - Placeholder: "Descripción adicional del empaque"

4. **Observaciones del Artículo** (textarea, opcional)

5. **Botones:**
   - Eliminar Artículo (si hay más de 1)
   - Agregar Otro Artículo

---

## Lógica Dinámica del Formulario

### Regla 1: Material solo para Cajas
```javascript
if (tipoUnidad !== "Caja") {
  // Ocultar y limpiar campos:
  tipoMaterial = null;
  color = null;
  tieneCintillo = false;
}
```

### Regla 2: Color y Cintillo solo para Plástico
```javascript
if (tipoMaterial !== "Plástico") {
  // Ocultar y limpiar campos:
  color = null;
  tieneCintillo = false;
}
```

### Ejemplo de Estado del Formulario (React):

```typescript
interface ArticuloFormulario {
  id_articulo: number | null;
  cantidad: number;
  unidad_envio: {
    id_tipo_unidad: number | null;
    id_tipo_material: number | null;
    id_color: number | null;
    tiene_cintillo: boolean;
    dimensiones: string;
    peso_maximo: number | null;
    descripcion: string;
  };
  observaciones: string;
}

const [articulos, setArticulos] = useState<ArticuloFormulario[]>([
  {
    id_articulo: null,
    cantidad: 1,
    unidad_envio: {
      id_tipo_unidad: null,
      id_tipo_material: null,
      id_color: null,
      tiene_cintillo: false,
      dimensiones: '',
      peso_maximo: null,
      descripcion: ''
    },
    observaciones: ''
  }
]);
```

---

## Validaciones Frontend

### Validación por Artículo:
```typescript
// Usando Yup
const articuloSchema = yup.object({
  id_articulo: yup.number().required('Seleccione un artículo'),
  cantidad: yup.number().min(1, 'Cantidad mínima: 1').required(),
  unidad_envio: yup.object({
    id_tipo_unidad: yup.number().required('Seleccione tipo de unidad'),
    id_tipo_material: yup.number().nullable().when('id_tipo_unidad', {
      is: (val) => val === ID_TIPO_CAJA, // Asume que tienes el ID
      then: (schema) => schema.required('Material requerido para cajas'),
      otherwise: (schema) => schema.nullable()
    }),
    id_color: yup.number().nullable(),
    tiene_cintillo: yup.boolean(),
    dimensiones: yup.string().max(100),
    peso_maximo: yup.number().positive().nullable(),
    descripcion: yup.string()
  }),
  observaciones: yup.string()
});
```

---

## Endpoints de Catálogos (para Selects)

### Obtener todos los catálogos
```
GET /api/catalogs
```

**Respuesta:**
```json
{
  "success": true,
  "data": {
    "tipos_unidad_envio": [
      { "id_tipo_unidad": 1, "nombre_tipo": "Caja", "activo": true },
      { "id_tipo_unidad": 2, "nombre_tipo": "Sobre", "activo": true },
      { "id_tipo_unidad": 3, "nombre_tipo": "Bulto", "activo": true }
    ],
    "tipos_material": [
      { 
        "id_tipo_material": 1, 
        "nombre_material": "Cartón", 
        "requiere_color": false,
        "requiere_cintillo": false,
        "activo": true 
      },
      { 
        "id_tipo_material": 2, 
        "nombre_material": "Plástico", 
        "requiere_color": true,
        "requiere_cintillo": true,
        "activo": true 
      },
      { 
        "id_tipo_material": 3, 
        "nombre_material": "Madera", 
        "requiere_color": false,
        "requiere_cintillo": false,
        "activo": true 
      }
    ],
    "colores": [
      { "id_color": 1, "nombre_color": "Rojo", "codigo_hex": "#FF0000" },
      { "id_color": 2, "nombre_color": "Azul", "codigo_hex": "#0000FF" },
      { "id_color": 3, "nombre_color": "Verde", "codigo_hex": "#00FF00" }
    ],
    "tipos_movimiento": [...],
    "metodos_envio": [...],
    "vehiculos": [...],
    "choferes": [...],
    "mensajeros": [...]
  }
}
```

**Nota:** Los catálogos `tipos_material` incluyen las propiedades `requiere_color` y `requiere_cintillo` que puedes usar para la lógica condicional.

---

## UX/UI Recomendaciones

### Flujo del Usuario:

1. Usuario llena información general de la nota
2. Click en "Agregar Artículo"
3. Busca y selecciona artículo
4. Ingresa cantidad
5. **Configuración de empaque:**
   - Selecciona "Tipo de Unidad"
   - Si seleccionó "Caja" → aparece selector de "Material"
   - Si seleccionó "Plástico" → aparecen "Color" y "Cintillo"
   - Opcionalmente llena dimensiones, peso, descripción
6. Puede agregar observaciones específicas del artículo
7. Click en "Agregar Otro Artículo" o "Guardar Nota"

### Visualización Recomendada:

```
┌─────────────────────────────────────────┐
│ Artículo #1                         [X] │
├─────────────────────────────────────────┤
│ Artículo: [Laptop Dell XPS 15    ▼]    │
│ Cantidad: [3                        ]    │
│                                          │
│ ┌────── Configuración de Empaque ─────┐ │
│ │ Tipo: [Caja              ▼]         │ │
│ │ Material: [Plástico      ▼]         │ │
│ │ Color: [Rojo             ▼]         │ │
│ │ ☑ Requiere cintillo                 │ │
│ │ Dimensiones: [30x40x50 cm      ]    │ │
│ │ Peso Máx: [25.5 kg             ]    │ │
│ │ Descripción: [___________________]  │ │
│ └─────────────────────────────────────┘ │
│                                          │
│ Observaciones: [Frágil - Manejar...  ]  │
└─────────────────────────────────────────┘

[+ Agregar Otro Artículo]
```

### Estados de Campos Condicionales:

**Ejemplo 1: Usuario selecciona "Sobre"**
```
Tipo: [Sobre ▼]
─ Material: (oculto)
─ Color: (oculto)
─ Cintillo: (oculto)
Dimensiones: [A4]
Peso Máx: [0.5 kg]
```

**Ejemplo 2: Usuario selecciona "Caja" → "Cartón"**
```
Tipo: [Caja ▼]
Material: [Cartón ▼]
─ Color: (oculto porque cartón no requiere)
─ Cintillo: (oculto)
Dimensiones: [40x50x60]
Peso Máx: [30 kg]
```

**Ejemplo 3: Usuario selecciona "Caja" → "Plástico"**
```
Tipo: [Caja ▼]
Material: [Plástico ▼]
Color: [Azul ▼] ← visible
☑ Requiere cintillo ← visible
Dimensiones: [30x40x50]
Peso Máx: [25 kg]
```

---

## Mensajes de Ayuda (Tooltips)

- **Tipo de Unidad:** "Seleccione cómo se empacará este artículo"
- **Material:** "Solo disponible para cajas. Seleccione el material de la caja"
- **Color:** "Solo para cajas de plástico. Seleccione el color"
- **Cintillo:** "Marque si la caja de plástico requiere cintillo de seguridad"
- **Dimensiones:** "Especifique las medidas aproximadas (Ej: 30x40x50 cm)"
- **Peso Máximo:** "Peso máximo que soporta el empaque en kg"

---

## Manejo de Errores

### Error de Validación (422):
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "articulos.0.unidad_envio.id_tipo_unidad": [
      "El tipo de unidad es obligatorio"
    ],
    "articulos.1.unidad_envio.id_tipo_material": [
      "El campo tipo de material no existe en tipos material"
    ]
  }
}
```

Mostrar errores específicos en cada campo del artículo correspondiente.

### Error de Servidor (500):
```json
{
  "success": false,
  "message": "Error al crear la nota de movimiento",
  "error": "mensaje técnico del error"
}
```

Mostrar toast genérico: "Error al crear la nota. Intente nuevamente."

---