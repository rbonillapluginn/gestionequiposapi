# Prompt para Frontend - Gestión de Permisos de Procesos por Usuario

## Contexto del Sistema

Necesito que crees una interfaz en **React + TypeScript + Material-UI v5** para gestionar permisos de procesos (estados de notas) por usuario. Este es un sistema de permisos granular que permite controlar qué usuarios pueden cambiar las notas de movimiento a estados específicos.

**Diferencia con otros permisos:**
- **Permisos de Secciones**: Controlan acceso a módulos por nivel de autorización
- **Permisos de Tipos de Movimiento**: Controlan qué tipos de notas puede crear un nivel
- **Permisos de Procesos** (ESTE): Control individual por usuario para cambiar estados de notas

---

## Estructura de la Base de Datos

### Tabla: `permisos_procesos_usuario`
```typescript
interface PermisoProcesoUsuario {
  id_permiso_proceso: number;
  id_usuario: number;
  id_estado: number;
  tiene_permiso: boolean;
  fecha_asignacion: string; // ISO DateTime
  id_usuario_asigna: number | null;
  usuario?: Usuario;
  estado?: EstadoNota;
  usuario_asigna?: Usuario;
}

interface EstadoNota {
  id_estado: number;
  nombre_estado: 'CREADA' | 'EN_TRANSITO' | 'RECIBIDA' | 'CANCELADA';
  descripcion: string;
  orden: number;
}
```

---

## Endpoints API Disponibles

### 1. Obtener permisos de un usuario
```typescript
GET /api/permisos-procesos/usuario/{id_usuario}

Response: {
  success: true,
  data: {
    usuario: {
      id_usuario: number;
      nombre_usuario: string;
      correo: string;
      id_nivel_autorizacion: number;
    },
    permisos: [
      {
        id_estado: number;
        nombre_estado: string;
        descripcion: string;
        tiene_permiso: boolean;
        fecha_asignacion: string | null;
      }
    ]
  }
}
```

### 2. Asignar múltiples permisos
```typescript
POST /api/permisos-procesos/asignar-multiple

Body: {
  id_usuario: number;
  permisos: [
    {
      id_estado: number;
      tiene_permiso: boolean;
    }
  ]
}
```

### 3. Asignar/actualizar permiso individual
```typescript
POST /api/permisos-procesos

Body: {
  id_usuario: number;
  id_estado: number;
  tiene_permiso: boolean;
}
```

### 4. Listar usuarios
```typescript
GET /api/users

Response: {
  success: true,
  data: {
    data: Usuario[];
    total: number;
    current_page: number;
  }
}
```

---

## Componentes a Crear

### 1. **PermisoProcesosPage.tsx** (Página Principal)

**Ubicación:** `src/pages/PermisoProcesosPage.tsx`

**Funcionalidad:**
- Listar todos los usuarios activos en una tabla
- Columnas: Nombre, Email, Nivel de Autorización, Acciones
- Botón "Gestionar Permisos" por cada usuario
- Búsqueda/filtro por nombre o email
- Paginación

**UI/UX:**
```
┌─────────────────────────────────────────────────────────────┐
│  Permisos de Procesos                          [+ Nuevo]    │
├─────────────────────────────────────────────────────────────┤
│  Buscar: [________________]                    Filtros ▼    │
├─────────────────────────────────────────────────────────────┤
│  Nombre Usuario    │ Email            │ Nivel │ Acciones   │
├─────────────────────────────────────────────────────────────┤
│  Juan Pérez        │ juan@empresa.com │ 3     │ [Permisos] │
│  María García      │ maria@empresa.com│ 2     │ [Permisos] │
│  Admin Sistema     │ admin@empresa.com│ 1     │ [Permisos] │
└─────────────────────────────────────────────────────────────┘
                        < 1 2 3 >
```

---

### 2. **AsignarPermisosDialog.tsx** (Modal de Asignación)

**Ubicación:** `src/components/permisos-procesos/AsignarPermisosDialog.tsx`

**Props:**
```typescript
interface AsignarPermisosDialogProps {
  open: boolean;
  onClose: () => void;
  usuario: Usuario;
  onSuccess: () => void;
}
```

**Funcionalidad:**
- Mostrar información del usuario (nombre, email, nivel)
- Listar TODOS los estados disponibles con switches
- Cada estado muestra:
  - Nombre del estado (badge con color según estado)
  - Descripción
  - Switch On/Off para el permiso
  - Fecha de asignación (si existe)
- Botón "Guardar Todos" que llama a `/asignar-multiple`
- Loading state durante guardado
- Confirmación al cerrar si hay cambios sin guardar

**UI/UX:**
```
┌─────────────────────────────────────────────────────────────┐
│  Permisos de Procesos - Juan Pérez                    [X]   │
├─────────────────────────────────────────────────────────────┤
│  Usuario: Juan Pérez (juan@empresa.com)                    │
│  Nivel de Autorización: Operador (Nivel 3)                 │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │ [✓] CREADA                              [Switch ON] │    │
│  │     Nota creada                                     │    │
│  │     Asignado: 04/11/2025 10:30                     │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │ [🚚] EN_TRANSITO                        [Switch ON] │    │
│  │     Nota en tránsito                               │    │
│  │     Asignado: 04/11/2025 10:30                     │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │ [📦] RECIBIDA                          [Switch OFF] │    │
│  │     Nota recibida                                  │    │
│  │     Sin asignar                                    │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
│  ┌────────────────────────────────────────────────────┐    │
│  │ [❌] CANCELADA                         [Switch OFF] │    │
│  │     Nota cancelada                                 │    │
│  │     Sin asignar                                    │    │
│  └────────────────────────────────────────────────────┘    │
│                                                             │
├─────────────────────────────────────────────────────────────┤
│                           [Cancelar]  [Guardar Cambios]    │
└─────────────────────────────────────────────────────────────┘
```

**Colores para Estados:**
```typescript
const estadoColors = {
  CREADA: 'info',      // Azul
  EN_TRANSITO: 'warning', // Naranja
  RECIBIDA: 'success',    // Verde
  CANCELADA: 'error'      // Rojo
};
```

---

### 3. **ResumenPermisosCard.tsx** (Componente de Resumen)

**Ubicación:** `src/components/permisos-procesos/ResumenPermisosCard.tsx`

**Props:**
```typescript
interface ResumenPermisosCardProps {
  usuario: Usuario;
  permisos: PermisoEstado[];
}
```

**Funcionalidad:**
- Mostrar resumen visual de permisos activos
- Chips con estados permitidos
- Indicador de progreso (X de 4 estados permitidos)

**UI/UX:**
```
┌─────────────────────────────────────────────┐
│  Permisos Activos: 2/4                      │
├─────────────────────────────────────────────┤
│  [CREADA] [EN_TRANSITO]                     │
│                                             │
│  ▓▓▓▓▓▓▓▓▓▓▓▓░░░░░░░░░░  50%               │
└─────────────────────────────────────────────┘
```

---

## Lógica de Negocio

### Estado Local del Dialog
```typescript
const [permisosTemp, setPermisosTemp] = useState<{
  [id_estado: number]: boolean;
}>({});

const [hasChanges, setHasChanges] = useState(false);
```

### Cargar Permisos al Abrir Dialog
```typescript
useEffect(() => {
  if (open && usuario) {
    cargarPermisosUsuario(usuario.id_usuario);
  }
}, [open, usuario]);

const cargarPermisosUsuario = async (idUsuario: number) => {
  try {
    setLoading(true);
    const response = await axios.get(
      `/api/permisos-procesos/usuario/${idUsuario}`
    );
    
    const permisosMap: { [key: number]: boolean } = {};
    response.data.data.permisos.forEach((p: any) => {
      permisosMap[p.id_estado] = p.tiene_permiso;
    });
    
    setPermisosTemp(permisosMap);
    setPermisosOriginales(permisosMap); // Para detectar cambios
  } catch (error) {
    enqueueSnackbar('Error al cargar permisos', { variant: 'error' });
  } finally {
    setLoading(false);
  }
};
```

### Guardar Cambios
```typescript
const handleGuardar = async () => {
  try {
    setSaving(true);
    
    // Convertir objeto a array
    const permisos = Object.entries(permisosTemp).map(([id_estado, tiene_permiso]) => ({
      id_estado: parseInt(id_estado),
      tiene_permiso
    }));
    
    await axios.post('/api/permisos-procesos/asignar-multiple', {
      id_usuario: usuario.id_usuario,
      permisos
    });
    
    enqueueSnackbar('Permisos actualizados exitosamente', { variant: 'success' });
    onSuccess();
    onClose();
  } catch (error: any) {
    enqueueSnackbar(
      error.response?.data?.message || 'Error al guardar permisos',
      { variant: 'error' }
    );
  } finally {
    setSaving(false);
  }
};
```

### Toggle de Permiso
```typescript
const handleTogglePermiso = (idEstado: number) => {
  setPermisosTemp(prev => ({
    ...prev,
    [idEstado]: !prev[idEstado]
  }));
  setHasChanges(true);
};
```

### Confirmación al Cerrar con Cambios
```typescript
const handleClose = () => {
  if (hasChanges) {
    if (window.confirm('Hay cambios sin guardar. ¿Desea salir?')) {
      onClose();
      setHasChanges(false);
    }
  } else {
    onClose();
  }
};
```

---

## Validaciones

### Frontend
1. ✅ Usuario debe estar activo para gestionar permisos
2. ✅ No permitir guardar sin cambios
3. ✅ Confirmar antes de cerrar con cambios pendientes
4. ✅ Loading states en carga y guardado

### Backend (Ya implementado)
1. ✅ `id_usuario` debe existir en tabla usuarios
2. ✅ `id_estado` debe existir en tabla estados_nota
3. ✅ Restricción UNIQUE para evitar duplicados (id_usuario + id_estado)
4. ✅ Al cambiar estado de nota, valida que usuario tenga permiso (403 si no tiene)

---

## Integración con Cambio de Estado

Cuando un usuario intenta cambiar el estado de una nota:

```typescript
// En NotaDetalleDialog o similar
const handleCambiarEstado = async (idEstado: number) => {
  try {
    await axios.patch(`/api/notas/${nota.id_nota}/status`, {
      id_estado: idEstado,
      observaciones: observaciones
    });
    
    enqueueSnackbar('Estado actualizado exitosamente', { variant: 'success' });
  } catch (error: any) {
    if (error.response?.status === 403) {
      // Error de permiso
      enqueueSnackbar(
        'No tiene permiso para cambiar la nota a este estado',
        { variant: 'error' }
      );
    } else {
      enqueueSnackbar('Error al cambiar estado', { variant: 'error' });
    }
  }
};
```

---

## Ejemplo de Componente EstadoCard

```typescript
interface EstadoCardProps {
  estado: EstadoNota;
  tienePermiso: boolean;
  fechaAsignacion: string | null;
  onToggle: (idEstado: number) => void;
}

const EstadoCard: React.FC<EstadoCardProps> = ({
  estado,
  tienePermiso,
  fechaAsignacion,
  onToggle
}) => {
  return (
    <Card 
      variant="outlined" 
      sx={{ 
        mb: 2,
        border: tienePermiso ? 2 : 1,
        borderColor: tienePermiso ? 'success.main' : 'divider'
      }}
    >
      <CardContent>
        <Box display="flex" justifyContent="space-between" alignItems="center">
          <Box>
            <Box display="flex" alignItems="center" gap={1}>
              <Chip 
                label={estado.nombre_estado}
                color={estadoColors[estado.nombre_estado]}
                size="small"
              />
              <Typography variant="body2" color="text.secondary">
                {estado.descripcion}
              </Typography>
            </Box>
            {fechaAsignacion && (
              <Typography variant="caption" color="text.secondary" sx={{ mt: 1 }}>
                Asignado: {new Date(fechaAsignacion).toLocaleString('es-ES')}
              </Typography>
            )}
            {!fechaAsignacion && !tienePermiso && (
              <Typography variant="caption" color="text.disabled" sx={{ mt: 1 }}>
                Sin asignar
              </Typography>
            )}
          </Box>
          <Switch
            checked={tienePermiso}
            onChange={() => onToggle(estado.id_estado)}
            color="success"
          />
        </Box>
      </CardContent>
    </Card>
  );
};
```

---

## Estructura de Archivos

```
src/
├── pages/
│   └── PermisoProcesosPage.tsx
├── components/
│   └── permisos-procesos/
│       ├── AsignarPermisosDialog.tsx
│       ├── ResumenPermisosCard.tsx
│       └── EstadoPermisoCard.tsx
├── services/
│   └── permisoProcesosService.ts
├── types/
│   └── permisos-procesos.types.ts
└── hooks/
    └── usePermisosProcesos.ts
```

---

## Service API

```typescript
// src/services/permisoProcesosService.ts
import axios from './axios';

export const permisoProcesosService = {
  getPermisosPorUsuario: async (idUsuario: number) => {
    const response = await axios.get(`/permisos-procesos/usuario/${idUsuario}`);
    return response.data;
  },

  asignarMultiple: async (idUsuario: number, permisos: { id_estado: number; tiene_permiso: boolean }[]) => {
    const response = await axios.post('/permisos-procesos/asignar-multiple', {
      id_usuario: idUsuario,
      permisos
    });
    return response.data;
  },

  verificarPermiso: async (idUsuario: number, idEstado: number) => {
    const response = await axios.get('/permisos-procesos/verificar', {
      params: { id_usuario: idUsuario, id_estado: idEstado }
    });
    return response.data;
  }
};
```

---

## Ruta en React Router

```typescript
// En tu router principal
{
  path: '/permisos-procesos',
  element: <PermisoProcesosPage />,
  meta: {
    requiresAuth: true,
    title: 'Permisos de Procesos'
  }
}
```

---

## Menú de Navegación

Agregar en el sidebar:

```typescript
{
  title: 'Permisos de Procesos',
  icon: <AssignmentIcon />,
  path: '/permisos-procesos',
  permission: 'permisos' // Solo visible para admins
}
```

---

## Notas Finales

1. **Permisos del Admin**: El usuario con nivel 1 debe tener todos los permisos por defecto
2. **Caché**: Considera cachear los permisos del usuario actual en Context/Redux
3. **Optimistic Updates**: Al toggle del switch, actualiza UI antes de confirmar con backend
4. **Bulk Actions**: Considera agregar "Activar Todos" / "Desactivar Todos"
5. **Historial**: Considera mostrar quién asignó el permiso y cuándo
6. **Exportación**: Agregar opción para exportar matriz de permisos a Excel

---

## Estados de Nota Disponibles

| ID | Nombre Estado | Descripción | Color UI |
|----|---------------|-------------|----------|
| 1  | CREADA        | Nota creada | Azul     |
| 2  | EN_TRANSITO   | Nota en tránsito | Naranja  |
| 3  | RECIBIDA      | Nota recibida | Verde    |
| 4  | CANCELADA     | Nota cancelada | Rojo     |

---

**¡Listo para implementar!** 🚀
