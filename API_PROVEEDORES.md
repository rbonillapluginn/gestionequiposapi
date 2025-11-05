# API de Proveedores - Documentación

## Endpoints Disponibles

Base URL: `/api/proveedores`

Todos los endpoints requieren autenticación con token Bearer.

---

## 1. Listar Proveedores

**GET** `/api/proveedores`

### Headers
```
Authorization: Bearer <token>
```

### Query Parameters

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `page` | integer | Número de página | `1` |
| `per_page` | integer | Registros por página (default: 15) | `20` |
| `search` | string | Búsqueda en nombre, RUC o contacto | `"ACME"` |
| `estado` | boolean | Filtrar por estado (1=activo, 0=inactivo) | `true` |

### Ejemplo de Request
```
GET /api/proveedores?page=1&per_page=15&search=ACME&estado=true
```

### Respuesta Exitosa (200)
```json
{
  "success": true,
  "message": "Proveedores obtenidos exitosamente",
  "data": {
    "current_page": 1,
    "data": [
      {
        "id_proveedor": 1,
        "nombre_proveedor": "ACME Corporation",
        "ruc": "20123456789",
        "telefono": "01-2345678",
        "correo": "contacto@acme.com",
        "direccion": "Av. Principal 123, Lima",
        "contacto": "Juan Pérez",
        "estado": true,
        "fecha_creacion": "2024-11-01 10:00:00",
        "fecha_actualizacion": "2024-11-03 14:30:00"
      },
      {
        "id_proveedor": 2,
        "nombre_proveedor": "Tech Solutions SAC",
        "ruc": "20987654321",
        "telefono": "01-9876543",
        "correo": "ventas@techsolutions.com",
        "direccion": "Calle Los Pinos 456, Miraflores",
        "contacto": "María García",
        "estado": true,
        "fecha_creacion": "2024-11-02 11:15:00",
        "fecha_actualizacion": null
      }
    ],
    "first_page_url": "http://api.test/api/proveedores?page=1",
    "from": 1,
    "last_page": 5,
    "last_page_url": "http://api.test/api/proveedores?page=5",
    "next_page_url": "http://api.test/api/proveedores?page=2",
    "path": "http://api.test/api/proveedores",
    "per_page": 15,
    "prev_page_url": null,
    "to": 15,
    "total": 73
  }
}
```

---

## 2. Crear Proveedor

**POST** `/api/proveedores`

### Headers
```
Authorization: Bearer <token>
Content-Type: application/json
```

### Body (JSON)

| Campo | Tipo | Requerido | Descripción | Validación |
|-------|------|-----------|-------------|------------|
| `nombre_proveedor` | string | Sí | Nombre del proveedor | max:150, único |
| `ruc` | string | No | RUC del proveedor | max:20 |
| `telefono` | string | No | Teléfono de contacto | max:20 |
| `correo` | string | No | Email de contacto | email, max:100 |
| `direccion` | string | No | Dirección física | max:255 |
| `contacto` | string | No | Nombre de persona de contacto | max:100 |
| `estado` | boolean | No | Estado (default: true) | boolean |

### Ejemplo de Request
```json
{
  "nombre_proveedor": "Distribuidora El Comercio SAC",
  "ruc": "20456789123",
  "telefono": "01-4567890",
  "correo": "ventas@elcomercio.com",
  "direccion": "Av. Javier Prado 2850, San Isidro",
  "contacto": "Carlos Rodríguez",
  "estado": true
}
```

### Respuesta Exitosa (201)
```json
{
  "success": true,
  "message": "Proveedor creado exitosamente",
  "data": {
    "id_proveedor": 15,
    "nombre_proveedor": "Distribuidora El Comercio SAC",
    "ruc": "20456789123",
    "telefono": "01-4567890",
    "correo": "ventas@elcomercio.com",
    "direccion": "Av. Javier Prado 2850, San Isidro",
    "contacto": "Carlos Rodríguez",
    "estado": true,
    "fecha_creacion": "2024-11-03 15:45:00",
    "fecha_actualizacion": null
  }
}
```

### Error de Validación (422)
```json
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "nombre_proveedor": [
      "El campo nombre del proveedor es obligatorio."
    ],
    "correo": [
      "El campo correo electrónico debe ser una dirección de correo válida."
    ]
  }
}
```

---

## 3. Mostrar Proveedor Específico

**GET** `/api/proveedores/{id}`

### Headers
```
Authorization: Bearer <token>
```

### URL Parameters
- `id` (integer, requerido): ID del proveedor

### Ejemplo de Request
```
GET /api/proveedores/15
```

### Respuesta Exitosa (200)
```json
{
  "success": true,
  "data": {
    "id_proveedor": 15,
    "nombre_proveedor": "Distribuidora El Comercio SAC",
    "ruc": "20456789123",
    "telefono": "01-4567890",
    "correo": "ventas@elcomercio.com",
    "direccion": "Av. Javier Prado 2850, San Isidro",
    "contacto": "Carlos Rodríguez",
    "estado": true,
    "fecha_creacion": "2024-11-03 15:45:00",
    "fecha_actualizacion": null
  }
}
```

### Error - No Encontrado (404)
```json
{
  "success": false,
  "message": "Proveedor no encontrado"
}
```

---

## 4. Actualizar Proveedor

**PUT** `/api/proveedores/{id}`

### Headers
```
Authorization: Bearer <token>
Content-Type: application/json
```

### URL Parameters
- `id` (integer, requerido): ID del proveedor

### Body (JSON)

Mismos campos que en crear, todos opcionales excepto `nombre_proveedor`.

### Ejemplo de Request
```json
{
  "nombre_proveedor": "Distribuidora El Comercio SAC",
  "ruc": "20456789123",
  "telefono": "01-4567890",
  "correo": "info@elcomercio.com",
  "direccion": "Av. Javier Prado 2850, Oficina 501, San Isidro",
  "contacto": "Carlos Rodríguez - Gerente de Ventas",
  "estado": true
}
```

### Respuesta Exitosa (200)
```json
{
  "success": true,
  "message": "Proveedor actualizado exitosamente",
  "data": {
    "id_proveedor": 15,
    "nombre_proveedor": "Distribuidora El Comercio SAC",
    "ruc": "20456789123",
    "telefono": "01-4567890",
    "correo": "info@elcomercio.com",
    "direccion": "Av. Javier Prado 2850, Oficina 501, San Isidro",
    "contacto": "Carlos Rodríguez - Gerente de Ventas",
    "estado": true,
    "fecha_creacion": "2024-11-03 15:45:00",
    "fecha_actualizacion": "2024-11-03 16:20:00"
  }
}
```

### Errores
- **404**: Proveedor no encontrado
- **422**: Error de validación (igual que en crear)

---

## 5. Eliminar Proveedor (Soft Delete)

**DELETE** `/api/proveedores/{id}`

### Headers
```
Authorization: Bearer <token>
```

### URL Parameters
- `id` (integer, requerido): ID del proveedor

### Ejemplo de Request
```
DELETE /api/proveedores/15
```

### Respuesta Exitosa (200)
```json
{
  "success": true,
  "message": "Proveedor eliminado exitosamente"
}
```

### Notas Importantes
- **No es eliminación física**: El proveedor solo se marca como inactivo (`estado = false`)
- Para reactivar un proveedor, usar el endpoint de actualización cambiando `estado` a `true`

### Error - No Encontrado (404)
```json
{
  "success": false,
  "message": "Proveedor no encontrado"
}
```

---

## Modelo de Datos

### Proveedor

```typescript
interface Proveedor {
  id_proveedor: number;
  nombre_proveedor: string;
  ruc: string | null;
  telefono: string | null;
  correo: string | null;
  direccion: string | null;
  contacto: string | null;
  estado: boolean;
  fecha_creacion: string; // ISO 8601
  fecha_actualizacion: string | null; // ISO 8601
}
```

---

## Casos de Uso Frontend

### 1. Listar Proveedores con Búsqueda y Filtros

```typescript
const fetchProveedores = async (page = 1, search = '', estado = null) => {
  try {
    const params = new URLSearchParams({
      page: page.toString(),
      per_page: '15'
    });
    
    if (search) params.append('search', search);
    if (estado !== null) params.append('estado', estado.toString());
    
    const response = await axios.get(`/api/proveedores?${params}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    
    return response.data.data;
  } catch (error) {
    console.error('Error al cargar proveedores:', error);
    throw error;
  }
};
```

---

### 2. Crear Nuevo Proveedor

```typescript
const crearProveedor = async (datos) => {
  try {
    const response = await axios.post('/api/proveedores', datos, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    enqueueSnackbar('Proveedor creado exitosamente', { variant: 'success' });
    return response.data.data;
  } catch (error) {
    if (error.response?.status === 422) {
      // Mostrar errores de validación
      Object.values(error.response.data.errors).forEach(errores => {
        errores.forEach(mensaje => {
          enqueueSnackbar(mensaje, { variant: 'error' });
        });
      });
    }
    throw error;
  }
};
```

---

### 3. Actualizar Proveedor

```typescript
const actualizarProveedor = async (id, datos) => {
  try {
    const response = await axios.put(`/api/proveedores/${id}`, datos, {
      headers: {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json'
      }
    });
    
    enqueueSnackbar('Proveedor actualizado exitosamente', { variant: 'success' });
    return response.data.data;
  } catch (error) {
    if (error.response?.status === 404) {
      enqueueSnackbar('Proveedor no encontrado', { variant: 'error' });
    }
    throw error;
  }
};
```

---

### 4. Eliminar (Desactivar) Proveedor

```typescript
const eliminarProveedor = async (id) => {
  try {
    await axios.delete(`/api/proveedores/${id}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
    
    enqueueSnackbar('Proveedor eliminado exitosamente', { variant: 'success' });
  } catch (error) {
    if (error.response?.status === 404) {
      enqueueSnackbar('Proveedor no encontrado', { variant: 'error' });
    }
    throw error;
  }
};
```

---

## Validaciones del Sistema

### Reglas de Validación

1. **nombre_proveedor**:
   - Obligatorio
   - Máximo 150 caracteres
   - Debe ser único en el sistema

2. **ruc**:
   - Opcional
   - Máximo 20 caracteres

3. **telefono**:
   - Opcional
   - Máximo 20 caracteres

4. **correo**:
   - Opcional
   - Debe ser un email válido
   - Máximo 100 caracteres

5. **direccion**:
   - Opcional
   - Máximo 255 caracteres

6. **contacto**:
   - Opcional
   - Máximo 100 caracteres

7. **estado**:
   - Opcional
   - Debe ser boolean (true/false, 1/0)
   - Default: `true`

---

## Notas Importantes

1. **Soft Delete**: La eliminación no borra físicamente el registro, solo cambia `estado` a `false`

2. **Timestamps Automáticos**: 
   - `fecha_creacion` se establece automáticamente al crear
   - `fecha_actualizacion` se actualiza automáticamente en cada modificación

3. **Búsqueda**: El parámetro `search` busca coincidencias en:
   - nombre_proveedor
   - ruc
   - contacto

4. **Unicidad**: El `nombre_proveedor` debe ser único en toda la tabla

---

## Ejemplos de Integración Frontend

### Tabla de Proveedores con Material-UI

```tsx
import { DataGrid } from '@mui/x-data-grid';

const ProveedoresTable = () => {
  const [proveedores, setProveedores] = useState([]);
  const [loading, setLoading] = useState(false);
  
  const columns = [
    { field: 'id_proveedor', headerName: 'ID', width: 70 },
    { field: 'nombre_proveedor', headerName: 'Nombre', width: 250 },
    { field: 'ruc', headerName: 'RUC', width: 120 },
    { field: 'telefono', headerName: 'Teléfono', width: 120 },
    { field: 'correo', headerName: 'Email', width: 200 },
    { field: 'contacto', headerName: 'Contacto', width: 180 },
    {
      field: 'estado',
      headerName: 'Estado',
      width: 100,
      renderCell: (params) => (
        <Chip 
          label={params.value ? 'Activo' : 'Inactivo'}
          color={params.value ? 'success' : 'default'}
          size="small"
        />
      )
    },
    {
      field: 'acciones',
      headerName: 'Acciones',
      width: 150,
      renderCell: (params) => (
        <>
          <IconButton onClick={() => handleEdit(params.row)}>
            <EditIcon />
          </IconButton>
          <IconButton onClick={() => handleDelete(params.row.id_proveedor)}>
            <DeleteIcon />
          </IconButton>
        </>
      )
    }
  ];
  
  return (
    <DataGrid
      rows={proveedores}
      columns={columns}
      getRowId={(row) => row.id_proveedor}
      loading={loading}
      pageSize={15}
      rowsPerPageOptions={[15, 30, 50]}
      checkboxSelection
      disableSelectionOnClick
    />
  );
};
```

---

## Fecha de Documentación
3 de noviembre de 2025
