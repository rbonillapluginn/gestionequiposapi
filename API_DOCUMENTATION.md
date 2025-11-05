# 📚 Documentación Completa de la API - Sistema de Gestión de Equipos

## 🔐 BASE URL
```
http://tu-dominio.com/api
```

---

## 📋 ÍNDICE
1. [Autenticación](#autenticación)
2. [Usuarios](#usuarios)
3. [Notas de Movimiento](#notas-de-movimiento)
4. [Artículos](#artículos)
5. [Tiendas](#tiendas)
6. [Catálogos](#catálogos)
7. [Permisos](#permisos)

---

## 🔑 AUTENTICACIÓN

### Registro de Usuario
**POST** `/auth/register`

**Body:**
```json
{
  "username": "usuario123",
  "password": "contraseña123",
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan.perez@email.com",
  "telefono": "1234-5678",
  "id_nivel_autorizacion": 1
}
```

**Respuesta Exitosa (201):**
```json
{
  "success": true,
  "message": "Usuario registrado exitosamente",
  "data": {
    "user": {
      "id_usuario": 1,
      "username": "usuario123",
      "nombre": "Juan",
      "apellido": "Pérez",
      "email": "juan.perez@email.com",
      "activo": true,
      "nivelAutorizacion": {...}
    },
    "token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Login
**POST** `/auth/login`

**Body:**
```json
{
  "username": "admin",
  "password": "admin123"
}
```

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "Login exitoso",
  "data": {
    "user": {...},
    "token": "2|xyz789...",
    "token_type": "Bearer"
  }
}
```

### Logout
**POST** `/auth/logout`
**Headers:** `Authorization: Bearer {token}`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "Logout exitoso"
}
```

### Obtener Usuario Actual
**GET** `/auth/me`
**Headers:** `Authorization: Bearer {token}`

### Cambiar Contraseña
**POST** `/auth/change-password`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "current_password": "contraseña_actual",
  "new_password": "nueva_contraseña",
  "new_password_confirmation": "nueva_contraseña"
}
```

---

## 👥 USUARIOS

**Todas las rutas requieren autenticación**

### Listar Usuarios
**GET** `/users`
**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `activo` (boolean): Filtrar por estado activo/inactivo
- `id_nivel_autorizacion` (int): Filtrar por nivel de autorización
- `search` (string): Buscar por nombre, apellido, username o email
- `per_page` (int): Elementos por página (default: 15)

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id_usuario": 1,
        "username": "admin",
        "nombre": "Administrador",
        "apellido": "del Sistema",
        "email": "admin@sistema.com",
        "activo": true,
        "nivelAutorizacion": {...}
      }
    ],
    "total": 10,
    "per_page": 15
  }
}
```

### Crear Usuario
**POST** `/users`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "username": "nuevouser",
  "password": "password123",
  "nombre": "Nombre",
  "apellido": "Apellido",
  "email": "email@example.com",
  "telefono": "1234-5678",
  "id_nivel_autorizacion": 2,
  "activo": true
}
```

### Obtener Usuario Específico
**GET** `/users/{id}`
**Headers:** `Authorization: Bearer {token}`

### Actualizar Usuario
**PUT** `/users/{id}`
**Headers:** `Authorization: Bearer {token}`

**Body:** (todos los campos son opcionales)
```json
{
  "nombre": "Nuevo Nombre",
  "apellido": "Nuevo Apellido",
  "email": "nuevo@email.com",
  "telefono": "9999-9999",
  "activo": false
}
```

### Eliminar Usuario (Desactivar)
**DELETE** `/users/{id}`
**Headers:** `Authorization: Bearer {token}`

### Resetear Contraseña de Usuario
**POST** `/users/{id}/reset-password`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "new_password": "nuevacontraseña123"
}
```

---

## 📦 NOTAS DE MOVIMIENTO

**Todas las rutas requieren autenticación**

### Listar Notas
**GET** `/notas`
**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `tipo_nota` (string): ENTRADA o SALIDA
- `id_estado` (int): ID del estado
- `id_tienda_origen` (int): ID tienda origen
- `id_tienda_destino` (int): ID tienda destino
- `fecha_inicio` (date): Fecha inicio (YYYY-MM-DD)
- `fecha_fin` (date): Fecha fin (YYYY-MM-DD)
- `numero_nota` (string): Búsqueda por número de nota
- `per_page` (int): Elementos por página

### Crear Nota de Movimiento
**POST** `/notas`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "tipo_nota": "SALIDA",
  "id_tipo_movimiento": 1,
  "id_tienda_origen": 1,
  "id_tienda_destino": 2,
  "proveedor_origen": null,
  "proveedor_destino": null,
  "id_metodo_envio": 1,
  "id_submetodo_envio": null,
  "id_vehiculo": 1,
  "id_chofer": 1,
  "hora_salida": "2024-10-30 14:00:00",
  "id_mensajero": null,
  "observaciones": "Envío urgente",
  "articulos": [
    {
      "id_articulo": 1,
      "cantidad": 5,
      "id_unidad_envio": 1,
      "observaciones": "Manejo con cuidado"
    },
    {
      "id_articulo": 2,
      "cantidad": 10,
      "id_unidad_envio": 2
    }
  ]
}
```

**Respuesta Exitosa (201):**
```json
{
  "success": true,
  "message": "Nota de movimiento creada exitosamente",
  "data": {
    "id_nota": 1,
    "numero_nota": "SAL-20241030-0001",
    "tipo_nota": "SALIDA",
    "estado": {
      "id_estado": 1,
      "nombre_estado": "CREADA"
    },
    "tiendaOrigen": {...},
    "tiendaDestino": {...},
    "detallesArticulos": [...]
  }
}
```

### Obtener Nota Específica
**GET** `/notas/{id}`
**Headers:** `Authorization: Bearer {token}`

**Respuesta incluye:**
- Información completa de la nota
- Tiendas de origen y destino
- Detalles de artículos
- Información de vehículo, chofer, mensajero
- Historial de cambios de estado

### Actualizar Estado de Nota
**PATCH** `/notas/{id}/status`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "id_estado": 2,
  "observaciones": "Nota enviada en camión #5"
}
```

**Estados disponibles:**
- 1: CREADA
- 2: EN_TRANSITO
- 3: RECIBIDA
- 4: CANCELADA

### Obtener Historial de Estados
**GET** `/notas/{id}/historial`
**Headers:** `Authorization: Bearer {token}`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": [
    {
      "id_historial": 1,
      "estadoAnterior": null,
      "estadoNuevo": {
        "id_estado": 1,
        "nombre_estado": "CREADA"
      },
      "usuario": {...},
      "fecha_cambio": "2024-10-30 10:00:00",
      "observaciones": "Nota creada"
    },
    {
      "id_historial": 2,
      "estadoAnterior": {
        "id_estado": 1,
        "nombre_estado": "CREADA"
      },
      "estadoNuevo": {
        "id_estado": 2,
        "nombre_estado": "EN_TRANSITO"
      },
      "usuario": {...},
      "fecha_cambio": "2024-10-30 14:00:00",
      "observaciones": "Nota enviada"
    }
  ]
}
```

### Dashboard / Monitor de Envíos
**GET** `/notas/dashboard`
**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `fecha_inicio` (date): Fecha inicio del rango
- `fecha_fin` (date): Fecha fin del rango

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": {
    "estadisticas": {
      "total_notas": 150,
      "notas_pendientes": 10,
      "notas_en_transito": 25,
      "notas_recibidas": 110,
      "notas_canceladas": 5
    },
    "notas_por_estado": [...],
    "notas_recientes": [...]
  }
}
```

---

## 📦 ARTÍCULOS

**Todas las rutas requieren autenticación**

### Listar Artículos
**GET** `/articulos`
**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `activo` (boolean): Filtrar por estado
- `id_categoria` (int): Filtrar por categoría
- `search` (string): Buscar por nombre, código de barra o número de serie
- `per_page` (int): Elementos por página

### Crear Artículo
**POST** `/articulos`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "nombre_articulo": "Laptop Dell XPS 15",
  "descripcion": "Laptop de alto rendimiento",
  "id_categoria": 1,
  "codigo_barra": "1234567890123",
  "numero_serie": null,
  "precio": 1200.50,
  "activo": true
}
```

**IMPORTANTE:** Debe proporcionar `codigo_barra` O `numero_serie` (al menos uno es obligatorio).

### Obtener Artículo Específico
**GET** `/articulos/{id}`
**Headers:** `Authorization: Bearer {token}`

### Actualizar Artículo
**PUT** `/articulos/{id}`
**Headers:** `Authorization: Bearer {token}`

### Eliminar Artículo (Desactivar)
**DELETE** `/articulos/{id}`
**Headers:** `Authorization: Bearer {token}`

### Buscar Artículo por Código
**POST** `/articulos/buscar-codigo`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "codigo": "1234567890123"
}
```

Busca por código de barra o número de serie.

---

## 🏪 TIENDAS

**Todas las rutas requieren autenticación**

### Listar Tiendas
**GET** `/tiendas`
**Headers:** `Authorization: Bearer {token}`

**Query Parameters:**
- `activo` (boolean): Filtrar por estado
- `search` (string): Buscar por nombre o código
- `per_page` (int): Elementos por página

### Crear Tienda
**POST** `/tiendas`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "nombre_tienda": "Tienda Central",
  "codigo_tienda": "TC001",
  "direccion": "Av. Principal #123",
  "telefono": "2222-3333",
  "email": "central@tiendas.com",
  "activo": true
}
```

### Obtener Tienda Específica
**GET** `/tiendas/{id}`
**Headers:** `Authorization: Bearer {token}`

### Actualizar Tienda
**PUT** `/tiendas/{id}`
**Headers:** `Authorization: Bearer {token}`

### Eliminar Tienda (Desactivar)
**DELETE** `/tiendas/{id}`
**Headers:** `Authorization: Bearer {token}`

---

## 📚 CATÁLOGOS

**Todas las rutas requieren autenticación**

### Obtener Todos los Catálogos
**GET** `/catalogs`
**Headers:** `Authorization: Bearer {token}`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": {
    "niveles_autorizacion": [...],
    "secciones": [...],
    "tipos_movimiento": [...],
    "departamentos": [...],
    "categorias_articulos": [...],
    "colores": [...],
    "tipos_unidad_envio": [...],
    "tipos_material": [...],
    "metodos_envio": [...],
    "estados_nota": [...],
    "vehiculos": [...],
    "choferes": [...],
    "mensajeros": [...]
  }
}
```

### Catálogos Individuales

#### Niveles de Autorización
**GET** `/catalogs/niveles-autorizacion`
**POST** `/catalogs/niveles-autorizacion`

**Body para POST:**
```json
{
  "nombre_nivel": "Supervisor",
  "descripcion": "Nivel de supervisor",
  "orden_jerarquico": 5
}
```

#### Departamentos
**GET** `/catalogs/departamentos`
**POST** `/catalogs/departamentos`

**Body para POST:**
```json
{
  "nombre_departamento": "Electrónica",
  "codigo_departamento": "ELECT",
  "descripcion": "Departamento de electrónica"
}
```

#### Categorías de Artículos
**GET** `/catalogs/categorias-articulos`
**POST** `/catalogs/categorias-articulos`

**Body para POST:**
```json
{
  "nombre_categoria": "Computadoras",
  "descripcion": "Equipos de cómputo"
}
```

#### Colores
**GET** `/catalogs/colores`
**POST** `/catalogs/colores`

**Body para POST:**
```json
{
  "nombre_color": "Rojo",
  "codigo_hex": "#FF0000"
}
```

#### Vehículos
**GET** `/catalogs/vehiculos`
**POST** `/catalogs/vehiculos`

**Body para POST:**
```json
{
  "numero_camion": "CAM-001",
  "placa": "ABC-1234",
  "modelo": "Ford F-150",
  "capacidad_carga": 1500.00
}
```

#### Choferes
**GET** `/catalogs/choferes`
**POST** `/catalogs/choferes`

**Body para POST:**
```json
{
  "nombre_completo": "Carlos Rodríguez",
  "licencia": "LIC123456",
  "telefono": "5555-5555"
}
```

#### Mensajeros
**GET** `/catalogs/mensajeros`
**POST** `/catalogs/mensajeros`

**Body para POST:**
```json
{
  "nombre_completo": "María López",
  "identificacion": "ID987654",
  "telefono": "6666-6666"
}
```

#### Métodos de Envío
**GET** `/catalogs/metodos-envio`

#### Tipos de Movimiento
**GET** `/catalogs/tipos-movimiento`
**POST** `/catalogs/tipos-movimiento`

**Body para POST:**
```json
{
  "nombre_tipo": "Transferencia interna",
  "codigo_tipo": "TRANS_INT",
  "descripcion": "Transferencia entre departamentos"
}
```

#### Estados de Nota
**GET** `/catalogs/estados-nota`

---

## 🔐 PERMISOS

**Todas las rutas requieren autenticación**

### Obtener Mis Permisos
**GET** `/permissions/mis-permisos`
**Headers:** `Authorization: Bearer {token}`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "data": {
    "usuario": {...},
    "permisos_secciones": [
      {
        "id_seccion": 1,
        "seccion": {
          "codigo_seccion": "USUARIOS",
          "nombre_seccion": "Usuarios"
        },
        "puede_leer": true,
        "puede_crear": true,
        "puede_modificar": true,
        "puede_eliminar": false
      }
    ],
    "permisos_tipos_movimiento": [...]
  }
}
```

### Verificar Permiso Específico
**POST** `/permissions/verificar-permiso`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "codigo_seccion": "USUARIOS",
  "accion": "crear"
}
```

**Acciones válidas:** `leer`, `crear`, `modificar`, `eliminar`

### Obtener Permisos de Secciones por Nivel
**GET** `/permissions/secciones/{idNivel}`
**Headers:** `Authorization: Bearer {token}`

### Actualizar Permisos de Secciones
**PUT** `/permissions/secciones/{idNivel}`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "permisos": [
    {
      "id_seccion": 1,
      "puede_leer": true,
      "puede_crear": true,
      "puede_modificar": false,
      "puede_eliminar": false
    },
    {
      "id_seccion": 2,
      "puede_leer": true,
      "puede_crear": false,
      "puede_modificar": false,
      "puede_eliminar": false
    }
  ]
}
```

### Obtener Permisos de Tipos de Movimiento por Nivel
**GET** `/permissions/tipos-movimiento/{idNivel}`
**Headers:** `Authorization: Bearer {token}`

### Actualizar Permisos de Tipos de Movimiento
**PUT** `/permissions/tipos-movimiento/{idNivel}`
**Headers:** `Authorization: Bearer {token}`

**Body:**
```json
{
  "permisos": [
    {
      "id_tipo_movimiento": 1,
      "puede_ejecutar": true,
      "requiere_autorizacion": false
    },
    {
      "id_tipo_movimiento": 2,
      "puede_ejecutar": true,
      "requiere_autorizacion": true
    }
  ]
}
```

---

## 🔄 VERIFICAR ESTADO DE LA API

**GET** `/health`

**Respuesta Exitosa (200):**
```json
{
  "success": true,
  "message": "API funcionando correctamente",
  "timestamp": "2024-10-30T10:30:00.000000Z",
  "version": "1.0.0"
}
```

---

## ⚠️ CÓDIGOS DE RESPUESTA HTTP

- **200 OK**: Solicitud exitosa
- **201 Created**: Recurso creado exitosamente
- **401 Unauthorized**: No autenticado o token inválido
- **403 Forbidden**: Sin permisos para realizar la acción
- **404 Not Found**: Recurso no encontrado
- **422 Unprocessable Entity**: Error de validación
- **500 Internal Server Error**: Error del servidor

---

## 📧 SISTEMA DE NOTIFICACIONES

El sistema envía correos automáticamente en los siguientes casos:

1. **Nota Creada**: Se notifica al creador y a los encargados de la tienda destino
2. **Nota Enviada**: Se notifica a los encargados de la tienda destino y al creador
3. **Nota Recibida**: Se notifica al creador, al que envió y a los encargados de la tienda origen

Las plantillas de correo incluyen variables como:
- `{{numero_nota}}`
- `{{tipo_nota}}`
- `{{tienda_origen}}`
- `{{tienda_destino}}`
- `{{fecha_envio}}`
- `{{usuario_crea}}`
- etc.

---

## 🚀 CONFIGURACIÓN INICIAL

### 1. Configurar Base de Datos
Actualiza tu archivo `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestionequipos
DB_USERNAME=root
DB_PASSWORD=
```

### 2. Ejecutar Migraciones
```bash
php artisan migrate
```

### 3. Ejecutar Seeders
```bash
php artisan db:seed
```

### 4. Usuario por Defecto
Después de ejecutar los seeders, puedes usar:
- **Username**: `admin`
- **Password**: `admin123`

---

## 📝 NOTAS IMPORTANTES

1. Todas las rutas protegidas requieren el header `Authorization: Bearer {token}`
2. El token se obtiene al hacer login o registro
3. Los artículos DEBEN tener código de barra O número de serie (al menos uno)
4. Las notas DEBEN tener origen (tienda o proveedor) y destino (tienda o proveedor)
5. Al cambiar estado de nota a EN_TRANSITO o RECIBIDA, se actualiza automáticamente la fecha y usuario correspondiente
6. El sistema mantiene un historial completo de cambios de estado
7. Los correos se envían automáticamente según el flujo de las notas

---

## 🆘 SOPORTE

Para consultas o problemas, contacta al equipo de desarrollo.

**Versión**: 1.0.0
**Última actualización**: 30 de Octubre, 2024