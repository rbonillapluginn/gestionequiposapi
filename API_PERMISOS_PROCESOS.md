# API de Permisos de Procesos por Usuario

## Descripción General

Este módulo permite gestionar permisos granulares por usuario para controlar quién puede cambiar el estado de las notas de movimiento. A diferencia de los permisos por nivel de autorización (que aplican a todos los usuarios de ese nivel), estos permisos son individuales por usuario.

**Tabla:** `permisos_procesos_usuario`

**Relaciones:**
- Usuario que tiene el permiso (`id_usuario`)
- Estado/Proceso al que aplica (`id_estado` → tabla `estados_nota`)
- Usuario que asignó el permiso (`id_usuario_asigna`)

---

## Endpoints Disponibles

### 1. Listar Permisos de Procesos

**Endpoint:** `GET /api/permisos-procesos`

**Autenticación:** Requerida (Bearer Token)

**Query Parameters:**
- `id_usuario` (opcional): Filtrar por usuario específico
- `id_estado` (opcional): Filtrar por estado/proceso específico
- `tiene_permiso` (opcional): Filtrar por estado del permiso (true/false)
- `per_page` (opcional): Registros por página (default: 15)

**Ejemplo Request:**
```bash
GET /api/permisos-procesos?id_usuario=5&tiene_permiso=true
Authorization: Bearer {token}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id_permiso_proceso": 1,
        "id_usuario": 5,
        "id_estado": 2,
        "tiene_permiso": true,
        "fecha_asignacion": "2025-11-04T10:30:00.000000Z",
        "id_usuario_asigna": 1,
        "usuario": {
          "id_usuario": 5,
          "nombre_usuario": "Juan Pérez",
          "correo": "juan@empresa.com"
        },
        "estado": {
          "id_estado": 2,
          "nombre_estado": "EN_TRANSITO",
          "descripcion": "Nota en tránsito"
        },
        "usuario_asigna": {
          "id_usuario": 1,
          "nombre_usuario": "Admin",
          "correo": "admin@empresa.com"
        }
      }
    ],
    "total": 1
  }
}
```

---

### 2. Obtener Permisos de Usuario Específico

**Endpoint:** `GET /api/permisos-procesos/usuario/{id_usuario}`

**Autenticación:** Requerida

**Descripción:** Retorna TODOS los estados con su estado de permiso para el usuario (incluye estados sin permiso asignado).

**Ejemplo Request:**
```bash
GET /api/permisos-procesos/usuario/5
Authorization: Bearer {token}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "data": {
    "usuario": {
      "id_usuario": 5,
      "nombre_usuario": "Juan Pérez",
      "correo": "juan@empresa.com",
      "id_nivel_autorizacion": 3
    },
    "permisos": [
      {
        "id_estado": 1,
        "nombre_estado": "CREADA",
        "descripcion": "Nota creada",
        "tiene_permiso": true,
        "fecha_asignacion": "2025-11-04T10:30:00.000000Z"
      },
      {
        "id_estado": 2,
        "nombre_estado": "EN_TRANSITO",
        "descripcion": "Nota en tránsito",
        "tiene_permiso": true,
        "fecha_asignacion": "2025-11-04T10:30:00.000000Z"
      },
      {
        "id_estado": 3,
        "nombre_estado": "RECIBIDA",
        "descripcion": "Nota recibida",
        "tiene_permiso": false,
        "fecha_asignacion": null
      },
      {
        "id_estado": 4,
        "nombre_estado": "CANCELADA",
        "descripcion": "Nota cancelada",
        "tiene_permiso": false,
        "fecha_asignacion": null
      }
    ]
  }
}
```

---

### 3. Asignar o Actualizar Permiso Individual

**Endpoint:** `POST /api/permisos-procesos`

**Autenticación:** Requerida

**Body Parameters:**
- `id_usuario` (requerido): ID del usuario
- `id_estado` (requerido): ID del estado/proceso
- `tiene_permiso` (requerido): true o false

**Ejemplo Request:**
```bash
POST /api/permisos-procesos
Authorization: Bearer {token}
Content-Type: application/json

{
  "id_usuario": 5,
  "id_estado": 2,
  "tiene_permiso": true
}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "message": "Permiso asignado exitosamente",
  "data": {
    "id_permiso_proceso": 1,
    "id_usuario": 5,
    "id_estado": 2,
    "tiene_permiso": true,
    "fecha_asignacion": "2025-11-04T10:30:00.000000Z",
    "id_usuario_asigna": 1,
    "usuario": {
      "id_usuario": 5,
      "nombre_usuario": "Juan Pérez"
    },
    "estado": {
      "id_estado": 2,
      "nombre_estado": "EN_TRANSITO"
    }
  }
}
```

---

### 4. Asignar Múltiples Permisos

**Endpoint:** `POST /api/permisos-procesos/asignar-multiple`

**Autenticación:** Requerida

**Descripción:** Permite asignar varios permisos a un usuario en una sola petición.

**Body Parameters:**
- `id_usuario` (requerido): ID del usuario
- `permisos` (requerido): Array de permisos
  - `id_estado` (requerido): ID del estado
  - `tiene_permiso` (requerido): true o false

**Ejemplo Request:**
```bash
POST /api/permisos-procesos/asignar-multiple
Authorization: Bearer {token}
Content-Type: application/json

{
  "id_usuario": 5,
  "permisos": [
    {
      "id_estado": 1,
      "tiene_permiso": true
    },
    {
      "id_estado": 2,
      "tiene_permiso": true
    },
    {
      "id_estado": 3,
      "tiene_permiso": false
    },
    {
      "id_estado": 4,
      "tiene_permiso": false
    }
  ]
}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "message": "Permisos asignados exitosamente",
  "data": [
    {
      "id_permiso_proceso": 1,
      "id_usuario": 5,
      "id_estado": 1,
      "tiene_permiso": true
    },
    {
      "id_permiso_proceso": 2,
      "id_usuario": 5,
      "id_estado": 2,
      "tiene_permiso": true
    }
  ]
}
```

---

### 5. Verificar Permiso

**Endpoint:** `GET /api/permisos-procesos/verificar`

**Autenticación:** Requerida

**Query Parameters:**
- `id_usuario` (requerido): ID del usuario
- `id_estado` (requerido): ID del estado/proceso

**Ejemplo Request:**
```bash
GET /api/permisos-procesos/verificar?id_usuario=5&id_estado=2
Authorization: Bearer {token}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "data": {
    "tiene_permiso": true
  }
}
```

---

### 6. Eliminar Permiso

**Endpoint:** `DELETE /api/permisos-procesos/{id}`

**Autenticación:** Requerida

**Descripción:** Elimina un permiso específico por su ID.

**Ejemplo Request:**
```bash
DELETE /api/permisos-procesos/1
Authorization: Bearer {token}
```

**Ejemplo Response:**
```json
{
  "success": true,
  "message": "Permiso eliminado exitosamente"
}
```

---

## Validación en Cambio de Estado

Cuando un usuario intenta cambiar el estado de una nota mediante:

**Endpoint:** `PATCH /api/notas/{id}/status`

El sistema automáticamente verifica:

1. ✅ Si el usuario tiene permiso para el nuevo estado
2. ❌ Si no tiene permiso, retorna error 403

**Ejemplo de Error:**
```json
{
  "success": false,
  "message": "No tiene permiso para cambiar la nota a este estado"
}
```

---

## Ejemplo de Flujo Completo (TypeScript)

```typescript
import axios from 'axios';

const API_URL = 'http://localhost:8000/api';
const token = 'tu_token_aqui';

// 1. Obtener todos los permisos de un usuario
async function obtenerPermisosUsuario(idUsuario: number) {
  const response = await axios.get(
    `${API_URL}/permisos-procesos/usuario/${idUsuario}`,
    {
      headers: { Authorization: `Bearer ${token}` }
    }
  );
  return response.data.data;
}

// 2. Asignar múltiples permisos
async function asignarPermisos(idUsuario: number) {
  const response = await axios.post(
    `${API_URL}/permisos-procesos/asignar-multiple`,
    {
      id_usuario: idUsuario,
      permisos: [
        { id_estado: 1, tiene_permiso: true },  // CREADA
        { id_estado: 2, tiene_permiso: true },  // EN_TRANSITO
        { id_estado: 3, tiene_permiso: false }, // RECIBIDA
        { id_estado: 4, tiene_permiso: false }  // CANCELADA
      ]
    },
    {
      headers: { Authorization: `Bearer ${token}` }
    }
  );
  return response.data;
}

// 3. Verificar permiso antes de cambiar estado
async function cambiarEstadoNota(idNota: number, idEstado: number) {
  try {
    const response = await axios.patch(
      `${API_URL}/notas/${idNota}/status`,
      { id_estado: idEstado },
      {
        headers: { Authorization: `Bearer ${token}` }
      }
    );
    return response.data;
  } catch (error: any) {
    if (error.response?.status === 403) {
      console.error('No tiene permiso para este proceso');
    }
    throw error;
  }
}

// 4. Listar usuarios con permiso para un proceso específico
async function usuariosConPermiso(idEstado: number) {
  const response = await axios.get(
    `${API_URL}/permisos-procesos`,
    {
      params: {
        id_estado: idEstado,
        tiene_permiso: true
      },
      headers: { Authorization: `Bearer ${token}` }
    }
  );
  return response.data.data;
}
```

---

## Notas Importantes

1. **Control Granular**: Estos permisos son por USUARIO, no por nivel de autorización
2. **Validación Automática**: El sistema valida automáticamente en `updateStatus()`
3. **Permisos del Admin**: El seeder asigna todos los permisos al usuario administrador
4. **Estados Disponibles**:
   - 1: CREADA
   - 2: EN_TRANSITO
   - 3: RECIBIDA
   - 4: CANCELADA

5. **Diferencia con Permisos por Nivel**:
   - **Permisos de Secciones**: Controlan acceso a módulos del sistema
   - **Permisos de Tipos de Movimiento**: Controlan qué tipos de notas puede crear un nivel
   - **Permisos de Procesos**: Controlan qué estados puede cambiar un usuario específico

---

## Seeder

Para asignar permisos iniciales al administrador:

```bash
php artisan db:seed --class=PermisoProcesoUsuarioSeeder
```

Este comando asigna automáticamente todos los permisos de estados al usuario administrador.
