# 📋 PROMPT COMPLETO PARA FRONTEND EN REACT

## Contexto del Proyecto

Necesito crear un frontend completo en React para consumir una API REST de Laravel que gestiona un sistema de notas de movimiento (entrada/salida) de artículos entre tiendas. El backend ya está completamente desarrollado y funcionando.

---

## 🎯 Objetivo Principal

Crear una aplicación React profesional con las siguientes características:
- Sistema de autenticación con tokens (JWT/Sanctum)
- Gestión completa de usuarios con niveles de autorización
- CRUD de notas de movimiento con flujo de estados
- Gestión de artículos con búsqueda por código de barras/serie
- Dashboard de monitoreo de envíos y recepciones
- Sistema de permisos granular por secciones
- Gestión de catálogos (tiendas, departamentos, métodos de envío, etc.)
- Interfaz moderna, responsive y amigable

---

## 🏗️ Stack Tecnológico Requerido

- **Framework**: React 18+
- **Enrutamiento**: React Router v6
- **Estado Global**: Context API + useReducer o Redux Toolkit
- **HTTP Client**: Axios
- **UI Framework**: Material-UI (MUI) v5 o Ant Design
- **Formularios**: React Hook Form + Yup/Zod para validación
- **Notificaciones**: React Toastify o Notistack
- **Tablas**: React Table (TanStack Table) o MUI DataGrid
- **Escaneo**: react-qr-barcode-scanner (opcional para códigos de barras)
- **Gestión de Tokens**: LocalStorage + Axios Interceptors

---

## 📡 Información de la API Backend

### Base URL
```
http://localhost:8000/api
```

### Sistema de Autenticación
- **Tipo**: Bearer Token (Laravel Sanctum)
- **Header requerido**: `Authorization: Bearer {token}`
- **Token obtenido tras login exitoso**

### Credenciales de Prueba
```
Username: admin
Password: admin123
Email: admin@sistema.com
```

---

## 🔐 ENDPOINTS DE AUTENTICACIÓN

### 1. Login
```http
POST /auth/login
Content-Type: application/json

Body:
{
  "username": "admin",
  "password": "admin123"
}

Response 200:
{
  "token": "1|abc123xyz...",
  "user": {
    "id_usuario": 1,
    "username": "admin",
    "nombre": "Administrador",
    "apellido": "del Sistema",
    "email": "admin@sistema.com",
    "nivel_autorizacion": {
      "id_nivel": 1,
      "nombre_nivel": "Super Administrador"
    }
  }
}
```

### 2. Logout
```http
POST /auth/logout
Authorization: Bearer {token}

Response 200:
{
  "message": "Sesión cerrada exitosamente"
}
```

### 3. Usuario Actual
```http
GET /auth/me
Authorization: Bearer {token}

Response 200:
{
  "id_usuario": 1,
  "username": "admin",
  "nombre": "Administrador",
  "apellido": "del Sistema",
  "email": "admin@sistema.com",
  "nivel_autorizacion": {...}
}
```

### 4. Cambiar Contraseña
```http
POST /auth/change-password
Authorization: Bearer {token}

Body:
{
  "current_password": "admin123",
  "new_password": "newpassword123",
  "new_password_confirmation": "newpassword123"
}
```

---

## 👥 ENDPOINTS DE USUARIOS

### Listar Usuarios
```http
GET /users?page=1&search=admin&nivel_id=1
Authorization: Bearer {token}

Response 200:
{
  "data": [
    {
      "id_usuario": 1,
      "username": "admin",
      "nombre": "Administrador",
      "apellido": "del Sistema",
      "email": "admin@sistema.com",
      "activo": true,
      "nivel_autorizacion": {
        "id_nivel": 1,
        "nombre_nivel": "Super Administrador"
      }
    }
  ],
  "current_page": 1,
  "total": 1,
  "per_page": 15
}
```

### Crear Usuario
```http
POST /users
Authorization: Bearer {token}

Body:
{
  "username": "usuario1",
  "password": "password123",
  "nombre": "Juan",
  "apellido": "Pérez",
  "email": "juan@ejemplo.com",
  "telefono": "0000-0000",
  "id_nivel_autorizacion": 2,
  "activo": true
}
```

### Actualizar Usuario
```http
PUT /users/{id}
Authorization: Bearer {token}

Body: (mismo que crear, password es opcional)
```

### Eliminar Usuario
```http
DELETE /users/{id}
Authorization: Bearer {token}
```

### Resetear Contraseña
```http
POST /users/{id}/reset-password
Authorization: Bearer {token}

Body:
{
  "new_password": "nuevapass123"
}
```

---

## 📝 ENDPOINTS DE NOTAS DE MOVIMIENTO

### Listar Notas
```http
GET /notas?page=1&tipo_nota=ENTRADA&id_estado=1&fecha_desde=2024-01-01&fecha_hasta=2024-12-31&numero_nota=NE-2024-001
Authorization: Bearer {token}

Query Params:
- page: número de página
- tipo_nota: ENTRADA o SALIDA
- id_estado: 1=CREADA, 2=EN_TRANSITO, 3=RECIBIDA, 4=CANCELADA
- fecha_desde: formato YYYY-MM-DD
- fecha_hasta: formato YYYY-MM-DD
- numero_nota: búsqueda por número
- id_tienda_origen: filtrar por tienda origen
- id_tienda_destino: filtrar por tienda destino
```

### Crear Nota
```http
POST /notas
Authorization: Bearer {token}

Body:
{
  "tipo_nota": "SALIDA",
  "id_tipo_movimiento": 1,
  "id_tienda_origen": 1,
  "id_tienda_destino": 2,
  "id_metodo_envio": 1,
  "id_vehiculo": 1,
  "id_chofer": 1,
  "id_mensajero": null,
  "id_submetodo_envio": null,
  "observaciones": "Transferencia de equipos",
  "articulos": [
    {
      "id_articulo": 1,
      "cantidad": 5
    },
    {
      "id_articulo": 2,
      "cantidad": 3
    }
  ]
}

Response 201:
{
  "id_nota_movimiento": 1,
  "numero_nota": "NS-2024-001",
  "tipo_nota": "SALIDA",
  "estado": {
    "id_estado": 1,
    "nombre_estado": "CREADA"
  },
  ...
}
```

### Ver Detalle de Nota
```http
GET /notas/{id}
Authorization: Bearer {token}

Response 200:
{
  "id_nota_movimiento": 1,
  "numero_nota": "NS-2024-001",
  "tipo_nota": "SALIDA",
  "tienda_origen": {...},
  "tienda_destino": {...},
  "metodo_envio": {...},
  "vehiculo": {...},
  "chofer": {...},
  "estado": {...},
  "usuario_crea": {...},
  "detalles_articulos": [
    {
      "articulo": {
        "id_articulo": 1,
        "nombre_articulo": "Laptop HP",
        "codigo_barras": "123456789"
      },
      "cantidad": 5
    }
  ],
  "fecha_creacion": "2024-01-15 10:30:00"
}
```

### Actualizar Estado de Nota
```http
PATCH /notas/{id}/status
Authorization: Bearer {token}

Body:
{
  "id_estado": 2,
  "observaciones": "Enviado en camión #1"
}

Estados:
- 1: CREADA
- 2: EN_TRANSITO (al enviar)
- 3: RECIBIDA (al recibir)
- 4: CANCELADA
```

### Ver Historial de Nota
```http
GET /notas/{id}/historial
Authorization: Bearer {token}

Response 200:
[
  {
    "id_historial": 1,
    "estado_anterior": "CREADA",
    "estado_nuevo": "EN_TRANSITO",
    "usuario": {
      "nombre": "Admin",
      "apellido": "Sistema"
    },
    "observaciones": "Enviado",
    "fecha_cambio": "2024-01-15 14:00:00"
  }
]
```

### Dashboard de Monitoreo
```http
GET /notas/dashboard
Authorization: Bearer {token}

Response 200:
{
  "estadisticas": {
    "total_notas_mes": 45,
    "notas_creadas": 5,
    "notas_en_transito": 12,
    "notas_recibidas": 25,
    "notas_canceladas": 3
  },
  "ultimas_notas": [...],
  "notas_pendientes_envio": [...],
  "notas_en_transito": [...]
}
```

### Generar Número de Nota
```http
POST /notas/generar-numero
Authorization: Bearer {token}

Body:
{
  "tipo_nota": "ENTRADA"
}

Response 200:
{
  "numero_nota": "NE-2024-025"
}
```

---

## 📦 ENDPOINTS DE ARTÍCULOS

### Listar Artículos
```http
GET /articulos?page=1&search=laptop&categoria_id=1
Authorization: Bearer {token}
```

### Crear Artículo
```http
POST /articulos
Authorization: Bearer {token}

Body:
{
  "nombre_articulo": "Laptop HP Pavilion",
  "descripcion": "Laptop para oficina",
  "codigo_barras": "123456789012",
  "numero_serie": "SN-ABC-123",
  "id_categoria": 1,
  "activo": true
}

IMPORTANTE: Debe tener al menos codigo_barras O numero_serie
```

### Buscar por Código/Serie
```http
POST /articulos/buscar-codigo
Authorization: Bearer {token}

Body:
{
  "codigo": "123456789012"
}

Response 200:
{
  "id_articulo": 1,
  "nombre_articulo": "Laptop HP Pavilion",
  "codigo_barras": "123456789012",
  "numero_serie": "SN-ABC-123",
  "categoria": {...}
}
```

---

## 🏪 ENDPOINTS DE TIENDAS

### Listar Tiendas
```http
GET /tiendas?activo=true
Authorization: Bearer {token}
```

### Crear Tienda
```http
POST /tiendas
Authorization: Bearer {token}

Body:
{
  "nombre_tienda": "Tienda Central",
  "direccion": "Av. Principal #123",
  "telefono": "0000-0000",
  "id_departamento": 1,
  "activo": true
}
```

---

## 📚 ENDPOINTS DE CATÁLOGOS

### Obtener Todos los Catálogos
```http
GET /catalogs
Authorization: Bearer {token}

Response 200:
{
  "niveles_autorizacion": [...],
  "estados_nota": [...],
  "tipos_movimiento": [...],
  "metodos_envio": [...],
  "tipos_unidad_envio": [...],
  "categorias_articulo": [...],
  "departamentos": [...],
  "tiendas": [...],
  "vehiculos": [...],
  "choferes": [...],
  "mensajeros": [...],
  "colores": [...],
  "tipos_material": [...]
}
```

### Catálogo Específico
```http
GET /catalogs/{tipo}
Authorization: Bearer {token}

Tipos disponibles:
- niveles-autorizacion
- estados-nota
- tipos-movimiento
- metodos-envio
- submetodos-envio
- tipos-unidad-envio
- categorias-articulo
- departamentos
- tiendas
- vehiculos
- choferes
- mensajeros
- colores
- tipos-material
```

---

## 🔒 ENDPOINTS DE PERMISOS

### Mis Permisos
```http
GET /permissions/mis-permisos
Authorization: Bearer {token}

Response 200:
{
  "permisos_secciones": [
    {
      "seccion": "USUARIOS",
      "puede_leer": true,
      "puede_crear": true,
      "puede_modificar": true,
      "puede_eliminar": true
    }
  ],
  "permisos_tipos_movimiento": [...]
}
```

### Verificar Permiso Específico
```http
POST /permissions/verificar-permiso
Authorization: Bearer {token}

Body:
{
  "seccion": "USUARIOS",
  "accion": "crear"
}

Response 200:
{
  "tiene_permiso": true
}
```

---

## 🎨 PANTALLAS A CREAR EN REACT

### 1. **Módulo de Autenticación**
- **Login** (`/login`)
  - Formulario con username y password
  - Botón de login
  - Manejo de errores
  - Redirección al dashboard tras login exitoso
  - Guardar token en localStorage

- **Cambiar Contraseña** (`/cambiar-password`)
  - Formulario con contraseña actual y nueva
  - Validación de coincidencia

### 2. **Layout Principal** (`/`)
- **Sidebar/Drawer** con menú de navegación:
  - Dashboard
  - Notas de Movimiento
  - Artículos
  - Tiendas
  - Usuarios
  - Catálogos
  - Permisos
  
- **Header/AppBar**:
  - Logo del sistema
  - Nombre del usuario logueado
  - Menú de usuario (Perfil, Cambiar contraseña, Cerrar sesión)
  
- **Footer**: Información básica

### 3. **Dashboard** (`/dashboard`)
- **Tarjetas de estadísticas**:
  - Total notas del mes
  - Notas creadas (pendientes de envío)
  - Notas en tránsito
  - Notas recibidas
  - Notas canceladas

- **Tabla de últimas notas** (últimas 10)
- **Tabla de notas pendientes de envío**
- **Tabla de notas en tránsito**
- **Gráfica de notas por estado** (opcional - ChartJS o Recharts)

### 4. **Módulo de Notas de Movimiento**

#### a) **Lista de Notas** (`/notas`)
- **Filtros**:
  - Tipo de nota (ENTRADA/SALIDA)
  - Estado (dropdown)
  - Rango de fechas (desde/hasta)
  - Número de nota
  - Tienda origen
  - Tienda destino
  - Botón "Buscar" y "Limpiar filtros"

- **Tabla con columnas**:
  - Número de nota
  - Tipo
  - Tienda origen
  - Tienda destino
  - Estado (badge con color)
  - Fecha creación
  - Acciones (Ver, Editar, Cambiar Estado, Historial)

- **Paginación**
- **Botón "Nueva Nota"** (superior derecha)

#### b) **Crear/Editar Nota** (`/notas/nuevo`, `/notas/editar/:id`)
- **Paso 1: Datos Generales**
  - Tipo de nota (Radio: ENTRADA/SALIDA)
  - Número de nota (auto-generado, readonly)
  - Tipo de movimiento (select)
  - Tienda origen (select)
  - Tienda destino (select)
  - Método de envío (select)
  - Si método es CAMION: Vehículo (select) + Chofer (select)
  - Si método es MENSAJERIA: Mensajero (select) + Submétodo (select)
  - Observaciones (textarea)

- **Paso 2: Artículos**
  - Buscador de artículos (por código de barras o nombre)
  - Botón "Escanear código" (opcional)
  - Tabla de artículos agregados:
    - Artículo
    - Código/Serie
    - Cantidad (input numérico)
    - Acciones (Eliminar)
  - Botón "Agregar artículo"

- **Botones**: Cancelar, Guardar como borrador, Crear

#### c) **Ver Detalle de Nota** (`/notas/ver/:id`)
- **Información general** (readonly):
  - Número de nota
  - Tipo y estado (con badge de color)
  - Tienda origen y destino
  - Método de envío (con detalles de vehículo/chofer/mensajero)
  - Usuario que creó
  - Fecha de creación
  - Usuario que envió + Fecha de envío (si aplica)
  - Usuario que recibió + Fecha de recepción (si aplica)
  - Observaciones

- **Tabla de artículos**:
  - Nombre
  - Código de barras
  - Serie
  - Cantidad

- **Botones de acción** (según estado y permisos):
  - Enviar (si estado = CREADA)
  - Recibir (si estado = EN_TRANSITO)
  - Cancelar (si estado = CREADA o EN_TRANSITO)
  - Ver historial
  - Imprimir (opcional)

#### d) **Historial de Nota** (Modal o página `/notas/historial/:id`)
- **Timeline/Lista de cambios**:
  - Estado anterior → Estado nuevo
  - Usuario que realizó el cambio
  - Fecha y hora
  - Observaciones

### 5. **Módulo de Artículos**

#### a) **Lista de Artículos** (`/articulos`)
- **Filtros**:
  - Búsqueda por nombre/código/serie
  - Categoría (select)
  - Estado (activo/inactivo)

- **Tabla**:
  - Nombre
  - Código de barras
  - Número de serie
  - Categoría
  - Estado (badge)
  - Acciones (Ver, Editar, Eliminar)

- **Botón "Nuevo Artículo"**

#### b) **Crear/Editar Artículo** (`/articulos/nuevo`, `/articulos/editar/:id`)
- Formulario:
  - Nombre del artículo *
  - Descripción
  - Código de barras (con validación, al menos uno requerido)
  - Número de serie (con validación, al menos uno requerido)
  - Categoría (select) *
  - Estado activo (checkbox)
  - Botón "Escanear código" (opcional)

- Validación: Al menos código de barras O número de serie debe estar lleno

### 6. **Módulo de Tiendas**

#### a) **Lista de Tiendas** (`/tiendas`)
- **Tabla**:
  - Nombre
  - Departamento
  - Dirección
  - Teléfono
  - Encargados (chips/badges)
  - Estado
  - Acciones

#### b) **Crear/Editar Tienda** (`/tiendas/nuevo`, `/tiendas/editar/:id`)
- Formulario:
  - Nombre *
  - Dirección
  - Teléfono
  - Departamento (select) *
  - Estado activo (checkbox)

### 7. **Módulo de Usuarios**

#### a) **Lista de Usuarios** (`/usuarios`)
- **Filtros**:
  - Búsqueda por nombre/username/email
  - Nivel de autorización (select)
  - Estado (activo/inactivo)

- **Tabla**:
  - Username
  - Nombre completo
  - Email
  - Nivel de autorización (badge)
  - Estado
  - Último login
  - Acciones (Ver, Editar, Resetear password, Eliminar)

#### b) **Crear/Editar Usuario** (`/usuarios/nuevo`, `/usuarios/editar/:id`)
- Formulario:
  - Username *
  - Password * (solo en crear)
  - Nombre *
  - Apellido *
  - Email *
  - Teléfono
  - Nivel de autorización (select) *
  - Estado activo (checkbox)

### 8. **Módulo de Catálogos** (`/catalogos`)
- **Pestañas (Tabs)**:
  - Niveles de autorización
  - Tipos de movimiento
  - Métodos de envío
  - Categorías de artículos
  - Departamentos
  - Vehículos
  - Choferes
  - Mensajeros
  - Colores
  - Tipos de material

- Cada pestaña con:
  - Tabla de registros
  - Botón crear
  - Acciones editar/eliminar
  - Formulario modal para crear/editar

### 9. **Módulo de Permisos** (`/permisos`)

#### a) **Permisos por Nivel** (`/permisos/niveles`)
- **Select de nivel de autorización**
- **Tabla de permisos por sección**:
  - Sección
  - Puede leer (checkbox)
  - Puede crear (checkbox)
  - Puede modificar (checkbox)
  - Puede eliminar (checkbox)

- **Tabla de permisos por tipo de movimiento**:
  - Tipo de movimiento
  - Puede ejecutar (checkbox)
  - Requiere autorización (checkbox)

- Botón "Guardar permisos"

---

## 🛠️ ESTRUCTURA DE CARPETAS SUGERIDA

```
src/
├── api/
│   ├── axios.js (configuración de axios)
│   ├── authApi.js
│   ├── notasApi.js
│   ├── articulosApi.js
│   ├── tiendasApi.js
│   ├── usuariosApi.js
│   ├── catalogosApi.js
│   └── permisosApi.js
├── components/
│   ├── common/
│   │   ├── LoadingSpinner.jsx
│   │   ├── ConfirmDialog.jsx
│   │   ├── SearchInput.jsx
│   │   ├── DataTable.jsx
│   │   └── StatusBadge.jsx
│   ├── layout/
│   │   ├── MainLayout.jsx
│   │   ├── Sidebar.jsx
│   │   ├── Header.jsx
│   │   └── Footer.jsx
│   ├── notas/
│   │   ├── NotasList.jsx
│   │   ├── NotaForm.jsx
│   │   ├── NotaDetail.jsx
│   │   ├── NotaHistorial.jsx
│   │   ├── ArticulosSelector.jsx
│   │   └── NotaFilters.jsx
│   ├── articulos/
│   │   ├── ArticulosList.jsx
│   │   ├── ArticuloForm.jsx
│   │   └── ArticuloFilters.jsx
│   ├── usuarios/
│   │   ├── UsersList.jsx
│   │   ├── UserForm.jsx
│   │   └── UserFilters.jsx
│   └── dashboard/
│       ├── StatsCard.jsx
│       ├── RecentNotasTable.jsx
│       └── NotasChart.jsx
├── context/
│   ├── AuthContext.jsx
│   └── CatalogContext.jsx
├── hooks/
│   ├── useAuth.js
│   ├── usePermissions.js
│   ├── useCatalogs.js
│   └── usePagination.js
├── pages/
│   ├── Login.jsx
│   ├── Dashboard.jsx
│   ├── notas/
│   │   ├── NotasPage.jsx
│   │   ├── NotaCreatePage.jsx
│   │   ├── NotaEditPage.jsx
│   │   └── NotaDetailPage.jsx
│   ├── articulos/
│   │   ├── ArticulosPage.jsx
│   │   ├── ArticuloCreatePage.jsx
│   │   └── ArticuloEditPage.jsx
│   ├── usuarios/
│   │   ├── UsuariosPage.jsx
│   │   ├── UsuarioCreatePage.jsx
│   │   └── UsuarioEditPage.jsx
│   ├── tiendas/
│   ├── catalogos/
│   └── permisos/
├── routes/
│   ├── AppRouter.jsx
│   ├── PrivateRoute.jsx
│   └── PublicRoute.jsx
├── utils/
│   ├── constants.js
│   ├── formatters.js
│   ├── validators.js
│   └── tokenManager.js
├── App.jsx
└── main.jsx
```

---

## 🔧 CONFIGURACIÓN INICIAL REQUERIDA

### 1. Configuración de Axios (src/api/axios.js)

```javascript
import axios from 'axios';

const API_BASE_URL = 'http://localhost:8000/api';

const apiClient = axios.create({
  baseURL: API_BASE_URL,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  },
});

// Interceptor para agregar token
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem('auth_token');
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

// Interceptor para manejar errores
apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
      window.location.href = '/login';
    }
    return Promise.reject(error);
  }
);

export default apiClient;
```

### 2. Context de Autenticación (src/context/AuthContext.jsx)

```javascript
import { createContext, useContext, useState, useEffect } from 'react';
import apiClient from '../api/axios';

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);
  const [token, setToken] = useState(localStorage.getItem('auth_token'));

  useEffect(() => {
    if (token) {
      loadUser();
    } else {
      setLoading(false);
    }
  }, [token]);

  const loadUser = async () => {
    try {
      const response = await apiClient.get('/auth/me');
      setUser(response.data);
    } catch (error) {
      logout();
    } finally {
      setLoading(false);
    }
  };

  const login = async (credentials) => {
    const response = await apiClient.post('/auth/login', credentials);
    const { token, user } = response.data;
    
    localStorage.setItem('auth_token', token);
    localStorage.setItem('user_data', JSON.stringify(user));
    
    setToken(token);
    setUser(user);
    
    return response.data;
  };

  const logout = async () => {
    try {
      await apiClient.post('/auth/logout');
    } catch (error) {
      console.error('Error during logout:', error);
    } finally {
      localStorage.removeItem('auth_token');
      localStorage.removeItem('user_data');
      setToken(null);
      setUser(null);
    }
  };

  const value = {
    user,
    token,
    loading,
    login,
    logout,
    isAuthenticated: !!token,
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
};

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within AuthProvider');
  }
  return context;
};
```

### 3. Hook de Permisos (src/hooks/usePermissions.js)

```javascript
import { useState, useEffect } from 'react';
import apiClient from '../api/axios';

export const usePermissions = () => {
  const [permissions, setPermissions] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    loadPermissions();
  }, []);

  const loadPermissions = async () => {
    try {
      const response = await apiClient.get('/permissions/mis-permisos');
      setPermissions(response.data);
    } catch (error) {
      console.error('Error loading permissions:', error);
    } finally {
      setLoading(false);
    }
  };

  const hasPermission = (seccion, accion) => {
    if (!permissions) return false;
    
    const permiso = permissions.permisos_secciones?.find(
      p => p.seccion === seccion
    );
    
    if (!permiso) return false;
    
    switch (accion) {
      case 'leer': return permiso.puede_leer;
      case 'crear': return permiso.puede_crear;
      case 'modificar': return permiso.puede_modificar;
      case 'eliminar': return permiso.puede_eliminar;
      default: return false;
    }
  };

  return { permissions, loading, hasPermission };
};
```

---

## 🎨 DISEÑO Y UX

### Paleta de Colores Sugerida
```css
--primary: #1976d2
--secondary: #dc004e
--success: #4caf50
--warning: #ff9800
--error: #f44336
--info: #2196f3

Estados de Nota:
--creada: #9e9e9e (gris)
--en-transito: #2196f3 (azul)
--recibida: #4caf50 (verde)
--cancelada: #f44336 (rojo)
```

### Componentes de UI Requeridos
- **Badges/Chips** para estados
- **DataTable** con ordenamiento, filtros y paginación
- **Modal/Dialog** para confirmaciones
- **Stepper** para crear notas (pasos)
- **Autocomplete** para seleccionar artículos, tiendas
- **DatePicker** para filtros de fechas
- **Toast/Snackbar** para notificaciones
- **Loading Spinner** global y por componente
- **Breadcrumbs** para navegación
- **Tooltips** para información adicional

---

## ✅ FUNCIONALIDADES ESPECÍFICAS REQUERIDAS

### 1. **Manejo de Estados de Nota**
- Mostrar badge con color según estado
- Deshabilitar botones según estado actual
- Validar transiciones de estado permitidas

### 2. **Búsqueda de Artículos**
- Buscar por nombre, código de barras o serie
- Autocompletado con debounce
- Mostrar resultados en dropdown
- Opción de escanear código (opcional)

### 3. **Validaciones de Formularios**
- Validar campos requeridos
- Validar formato de email
- Validar que artículo tenga al menos código o serie
- Validar contraseña segura
- Mostrar errores en tiempo real

### 4. **Manejo de Errores**
- Mostrar errores de la API con mensajes claros
- Manejar errores 401 (redirigir a login)
- Manejar errores 403 (sin permisos)
- Manejar errores 404, 500, etc.

### 5. **Optimizaciones**
- Lazy loading de rutas
- Caché de catálogos
- Debounce en búsquedas
- Paginación en tablas grandes
- Loading states en botones

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

### Fase 1: Setup y Autenticación
- [ ] Crear proyecto React con Vite
- [ ] Instalar dependencias (MUI, React Router, Axios, React Hook Form, etc.)
- [ ] Configurar Axios con interceptors
- [ ] Crear AuthContext y AuthProvider
- [ ] Implementar página de Login
- [ ] Implementar PrivateRoute
- [ ] Implementar Layout principal (Sidebar, Header)

### Fase 2: Dashboard
- [ ] Crear página de Dashboard
- [ ] Implementar tarjetas de estadísticas
- [ ] Implementar tablas de últimas notas
- [ ] Conectar con endpoint de dashboard

### Fase 3: Módulo de Notas
- [ ] Crear lista de notas con filtros y paginación
- [ ] Crear formulario de nueva nota (paso a paso)
- [ ] Implementar búsqueda y selección de artículos
- [ ] Crear vista de detalle de nota
- [ ] Implementar cambio de estados
- [ ] Crear modal/página de historial
- [ ] Implementar validaciones

### Fase 4: Módulo de Artículos
- [ ] Crear lista de artículos
- [ ] Crear formulario de artículo
- [ ] Implementar búsqueda por código
- [ ] Implementar validaciones (código o serie requerido)

### Fase 5: Módulo de Usuarios
- [ ] Crear lista de usuarios
- [ ] Crear formulario de usuario
- [ ] Implementar reseteo de contraseña
- [ ] Implementar cambio de contraseña

### Fase 6: Módulo de Tiendas
- [ ] Crear CRUD de tiendas
- [ ] Conectar con departamentos

### Fase 7: Módulo de Catálogos
- [ ] Crear interfaz con tabs
- [ ] Implementar CRUD genérico para cada catálogo
- [ ] Cargar catálogos al inicio y cachear

### Fase 8: Módulo de Permisos
- [ ] Crear interfaz de gestión de permisos
- [ ] Implementar hook usePermissions
- [ ] Ocultar/deshabilitar elementos según permisos

### Fase 9: Optimizaciones y Pruebas
- [ ] Implementar lazy loading
- [ ] Agregar loading states
- [ ] Manejo robusto de errores
- [ ] Responsive design
- [ ] Pruebas de usuario
- [ ] Optimización de rendimiento

---

## 🚀 COMANDOS DE INICIO

```bash
# Crear proyecto
npm create vite@latest gestionequipos-frontend -- --template react

cd gestionequipos-frontend

# Instalar dependencias principales
npm install react-router-dom axios @mui/material @mui/icons-material @emotion/react @emotion/styled

# Instalar dependencias de formularios y validación
npm install react-hook-form yup @hookform/resolvers

# Instalar notificaciones
npm install react-toastify

# Instalar utilidades
npm install date-fns

# Iniciar desarrollo
npm run dev
```

---

## 📝 NOTAS IMPORTANTES

1. **Todas las peticiones** requieren el header `Authorization: Bearer {token}` excepto el login
2. **Los errores 401** deben redirigir automáticamente al login
3. **Los permisos** deben consultarse al inicio y guardarse en contexto
4. **Los catálogos** deben cargarse al inicio y cachearse
5. **Los estados de nota** tienen un flujo específico: CREADA → EN_TRANSITO → RECIBIDA (o CANCELADA en cualquier momento)
6. **La paginación** viene en el formato Laravel estándar con `data`, `current_page`, `total`, `per_page`
7. **Los artículos** DEBEN tener al menos `codigo_barras` O `numero_serie`
8. **Al crear nota**, el número se genera automáticamente según el tipo

---

## 🎯 RESULTADO ESPERADO

Una aplicación React profesional, moderna y completamente funcional que permita:
- Gestionar el ciclo completo de notas de movimiento
- Control granular de permisos por usuario
- Interfaz intuitiva y responsive
- Manejo robusto de errores
- Experiencia de usuario fluida
- Preparada para producción

---

**¿TIENES TODA LA INFORMACIÓN NECESARIA PARA COMENZAR?**

Si necesitas aclaración sobre algún endpoint, flujo o funcionalidad específica, por favor pregunta antes de comenzar la implementación.
