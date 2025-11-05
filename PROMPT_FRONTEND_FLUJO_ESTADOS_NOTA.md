# PROMPT: Flujo de Estados de Notas de Movimiento - Frontend

## Resumen
Implementar la gestión completa del ciclo de vida de una nota de movimiento a través de sus diferentes estados, con transiciones controladas, historial y notificaciones automáticas.

---

## Estados del Sistema

### 1. CREADA (id: 1)
- **Descripción:** Nota creada pero no enviada
- **Color sugerido:** Azul claro (#2196F3)
- **Icono:** ✏️ Edit / Draft
- **Permisos:** Usuario creador puede editar o cancelar

### 2. EN_TRANSITO (id: 2)
- **Descripción:** Nota enviada, en camino
- **Color sugerido:** Naranja (#FF9800)
- **Icono:** 🚚 Local Shipping / Truck
- **Permisos:** Solo puede pasar a RECIBIDA o CANCELADA

### 3. RECIBIDA (id: 3)
- **Descripción:** Nota recibida en destino
- **Color sugerido:** Verde (#4CAF50)
- **Icono:** ✅ Check Circle / Done
- **Permisos:** Estado final, no se puede cambiar

### 4. CANCELADA (id: 4)
- **Descripción:** Nota cancelada
- **Color sugerido:** Rojo (#F44336)
- **Icono:** ❌ Cancel / Close
- **Permisos:** Estado final, no se puede cambiar

---

## Diagrama de Flujo

```
CREADA (1)
    ↓
    ├──→ EN_TRANSITO (2)  ← (Enviar nota)
    │       ↓
    │       └──→ RECIBIDA (3)  ← (Confirmar recepción)
    │
    └──→ CANCELADA (4)  ← (Cancelar en cualquier momento antes de RECIBIDA)
```

### Transiciones Permitidas:

| Estado Actual | Puede cambiar a | Acción | Restricciones |
|---------------|-----------------|--------|---------------|
| CREADA | EN_TRANSITO | "Enviar Nota" | Solo creador o admin |
| CREADA | CANCELADA | "Cancelar Nota" | Solo creador o admin |
| EN_TRANSITO | RECIBIDA | "Confirmar Recepción" | Solo receptor o admin |
| EN_TRANSITO | CANCELADA | "Cancelar Nota" | Solo creador o admin |
| RECIBIDA | - | - | Estado final |
| CANCELADA | - | - | Estado final |

---

## Endpoints del Backend

### 1. Actualizar Estado de Nota

**PATCH /api/notas/{id}/status**

Headers:
```
Authorization: Bearer <token>
Content-Type: application/json
```

Payload:
```json
{
  "id_estado": 2,
  "observaciones": "Nota enviada en vehículo #3 con chofer Juan Pérez"
}
```

Respuesta Exitosa (200):
```json
{
  "success": true,
  "message": "Estado actualizado exitosamente",
  "data": {
    "id_nota": 15,
    "numero_nota": "SAL-20241103-0001",
    "tipo_nota": "SALIDA",
    "id_estado": 2,
    "fecha_creacion": "2024-11-03 10:00:00",
    "fecha_envio": "2024-11-03 14:30:00",
    "fecha_recepcion": null,
    "id_usuario_crea": 5,
    "id_usuario_envia": 5,
    "id_usuario_recibe": null,
    "estado": {
      "id_estado": 2,
      "nombre_estado": "EN_TRANSITO",
      "descripcion": "Nota enviada, en camino",
      "orden": 2
    }
  }
}
```

Errores:
```json
// 404 - Nota no encontrada
{
  "success": false,
  "message": "Nota no encontrada"
}

// 422 - Validación
{
  "success": false,
  "message": "Error de validación",
  "errors": {
    "id_estado": ["El campo id estado es obligatorio."]
  }
}
```

---

### 2. Obtener Historial de Estados

**GET /api/notas/{id}/historial**

Headers:
```
Authorization: Bearer <token>
```

Respuesta:
```json
{
  "success": true,
  "data": [
    {
      "id_historial": 3,
      "id_nota": 15,
      "id_estado_anterior": 2,
      "id_estado_nuevo": 3,
      "fecha_cambio": "2024-11-03 16:45:00",
      "observaciones": "Recibido conforme",
      "estadoAnterior": {
        "id_estado": 2,
        "nombre_estado": "EN_TRANSITO"
      },
      "estadoNuevo": {
        "id_estado": 3,
        "nombre_estado": "RECIBIDA"
      },
      "usuario": {
        "id_usuario": 8,
        "nombre": "María",
        "apellido": "González"
      }
    },
    {
      "id_historial": 2,
      "id_nota": 15,
      "id_estado_anterior": 1,
      "id_estado_nuevo": 2,
      "fecha_cambio": "2024-11-03 14:30:00",
      "observaciones": "Enviado en vehículo #3",
      "estadoAnterior": {
        "id_estado": 1,
        "nombre_estado": "CREADA"
      },
      "estadoNuevo": {
        "id_estado": 2,
        "nombre_estado": "EN_TRANSITO"
      },
      "usuario": {
        "id_usuario": 5,
        "nombre": "Carlos",
        "apellido": "Ramírez"
      }
    },
    {
      "id_historial": 1,
      "id_nota": 15,
      "id_estado_anterior": null,
      "id_estado_nuevo": 1,
      "fecha_cambio": "2024-11-03 10:00:00",
      "observaciones": "Nota creada",
      "estadoAnterior": null,
      "estadoNuevo": {
        "id_estado": 1,
        "nombre_estado": "CREADA"
      },
      "usuario": {
        "id_usuario": 5,
        "nombre": "Carlos",
        "apellido": "Ramírez"
      }
    }
  ]
}
```

---

## Comportamiento Automático del Backend

### Al cambiar a EN_TRANSITO (id: 2):
- ✅ Registra `fecha_envio` con timestamp actual
- ✅ Registra `id_usuario_envia` con el usuario que hace el cambio
- ✅ Envía notificación por email al destinatario
- ✅ Crea registro en `historial_estado_nota`

### Al cambiar a RECIBIDA (id: 3):
- ✅ Registra `fecha_recepcion` con timestamp actual
- ✅ Registra `id_usuario_recibe` con el usuario que hace el cambio
- ✅ Envía notificación por email al remitente
- ✅ Crea registro en `historial_estado_nota`

### Al cambiar a CANCELADA (id: 4):
- ✅ Solo actualiza estado
- ✅ Crea registro en `historial_estado_nota`
- ❌ No envía notificaciones automáticas

---

## Implementación Frontend

### Componente: Vista de Detalle de Nota

#### Sección de Estado Actual

```tsx
import { Chip, Box, Typography } from '@mui/material';
import CheckCircleIcon from '@mui/icons-material/CheckCircle';
import LocalShippingIcon from '@mui/icons-material/LocalShipping';
import EditIcon from '@mui/icons-material/Edit';
import CancelIcon from '@mui/icons-material/Cancel';

const EstadoChip = ({ estado }) => {
  const configs = {
    1: { label: 'CREADA', color: 'primary', icon: <EditIcon /> },
    2: { label: 'EN TRÁNSITO', color: 'warning', icon: <LocalShippingIcon /> },
    3: { label: 'RECIBIDA', color: 'success', icon: <CheckCircleIcon /> },
    4: { label: 'CANCELADA', color: 'error', icon: <CancelIcon /> }
  };
  
  const config = configs[estado.id_estado] || configs[1];
  
  return (
    <Chip 
      label={config.label} 
      color={config.color} 
      icon={config.icon}
      size="large"
    />
  );
};

const DetalleNota = ({ nota }) => {
  return (
    <Box>
      <Typography variant="h6">Estado Actual</Typography>
      <EstadoChip estado={nota.estado} />
      
      <Box mt={2}>
        <Typography variant="body2" color="textSecondary">
          Creada: {formatDate(nota.fecha_creacion)} por {nota.usuarioCrea.nombre}
        </Typography>
        
        {nota.fecha_envio && (
          <Typography variant="body2" color="textSecondary">
            Enviada: {formatDate(nota.fecha_envio)} por {nota.usuarioEnvia.nombre}
          </Typography>
        )}
        
        {nota.fecha_recepcion && (
          <Typography variant="body2" color="textSecondary">
            Recibida: {formatDate(nota.fecha_recepcion)} por {nota.usuarioRecibe.nombre}
          </Typography>
        )}
      </Box>
    </Box>
  );
};
```

---

#### Botones de Acción según Estado

```tsx
const AccionesNota = ({ nota, onCambiarEstado }) => {
  const estadoActual = nota.id_estado;
  
  // Determinar qué acciones mostrar
  const puedeEnviar = estadoActual === 1; // CREADA
  const puedeRecibir = estadoActual === 2; // EN_TRANSITO
  const puedeCancelar = estadoActual === 1 || estadoActual === 2;
  const esFinal = estadoActual === 3 || estadoActual === 4;
  
  if (esFinal) {
    return (
      <Alert severity="info">
        Esta nota está en estado final y no puede modificarse.
      </Alert>
    );
  }
  
  return (
    <Box display="flex" gap={2}>
      {puedeEnviar && (
        <Button
          variant="contained"
          color="warning"
          startIcon={<LocalShippingIcon />}
          onClick={() => onCambiarEstado(2, 'Enviar Nota')}
        >
          Enviar Nota
        </Button>
      )}
      
      {puedeRecibir && (
        <Button
          variant="contained"
          color="success"
          startIcon={<CheckCircleIcon />}
          onClick={() => onCambiarEstado(3, 'Confirmar Recepción')}
        >
          Confirmar Recepción
        </Button>
      )}
      
      {puedeCancelar && (
        <Button
          variant="outlined"
          color="error"
          startIcon={<CancelIcon />}
          onClick={() => onCambiarEstado(4, 'Cancelar Nota')}
        >
          Cancelar Nota
        </Button>
      )}
    </Box>
  );
};
```

---

#### Diálogo de Confirmación

```tsx
import { Dialog, DialogTitle, DialogContent, DialogActions, TextField, Button } from '@mui/material';

const DialogoCambiarEstado = ({ open, onClose, onConfirmar, tituloAccion }) => {
  const [observaciones, setObservaciones] = useState('');
  
  const handleConfirmar = () => {
    onConfirmar(observaciones);
    setObservaciones('');
  };
  
  return (
    <Dialog open={open} onClose={onClose} maxWidth="sm" fullWidth>
      <DialogTitle>{tituloAccion}</DialogTitle>
      <DialogContent>
        <TextField
          label="Observaciones (opcional)"
          multiline
          rows={4}
          fullWidth
          value={observaciones}
          onChange={(e) => setObservaciones(e.target.value)}
          placeholder="Agregue comentarios sobre este cambio de estado..."
          sx={{ mt: 2 }}
        />
      </DialogContent>
      <DialogActions>
        <Button onClick={onClose}>Cancelar</Button>
        <Button onClick={handleConfirmar} variant="contained">
          Confirmar
        </Button>
      </DialogActions>
    </Dialog>
  );
};
```

---

#### Función para Cambiar Estado

```tsx
const cambiarEstadoNota = async (idNota, nuevoEstado, observaciones) => {
  try {
    const response = await axios.patch(
      `/api/notas/${idNota}/status`,
      {
        id_estado: nuevoEstado,
        observaciones: observaciones || null
      },
      {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    );
    
    if (response.data.success) {
      enqueueSnackbar('Estado actualizado exitosamente', { variant: 'success' });
      
      // Recargar datos de la nota
      recargarNota();
      
      // Opcional: recargar historial
      recargarHistorial();
    }
  } catch (error) {
    if (error.response?.status === 422) {
      enqueueSnackbar('Error de validación', { variant: 'error' });
    } else if (error.response?.status === 404) {
      enqueueSnackbar('Nota no encontrada', { variant: 'error' });
    } else {
      enqueueSnackbar('Error al actualizar estado', { variant: 'error' });
    }
  }
};
```

---

### Componente: Timeline de Historial

```tsx
import { Timeline, TimelineItem, TimelineSeparator, TimelineConnector, TimelineContent, TimelineDot } from '@mui/lab';

const HistorialEstados = ({ idNota }) => {
  const [historial, setHistorial] = useState([]);
  
  useEffect(() => {
    cargarHistorial();
  }, [idNota]);
  
  const cargarHistorial = async () => {
    try {
      const response = await axios.get(`/api/notas/${idNota}/historial`, {
        headers: { Authorization: `Bearer ${token}` }
      });
      
      if (response.data.success) {
        setHistorial(response.data.data);
      }
    } catch (error) {
      console.error('Error al cargar historial:', error);
    }
  };
  
  return (
    <Box>
      <Typography variant="h6" gutterBottom>
        Historial de Estados
      </Typography>
      
      <Timeline position="right">
        {historial.map((item, index) => (
          <TimelineItem key={item.id_historial}>
            <TimelineSeparator>
              <TimelineDot color={getColorEstado(item.estadoNuevo.id_estado)} />
              {index < historial.length - 1 && <TimelineConnector />}
            </TimelineSeparator>
            
            <TimelineContent>
              <Paper elevation={2} sx={{ p: 2 }}>
                <Typography variant="subtitle1" fontWeight="bold">
                  {item.estadoNuevo.nombre_estado}
                </Typography>
                
                <Typography variant="body2" color="textSecondary">
                  {formatDate(item.fecha_cambio)}
                </Typography>
                
                <Typography variant="body2">
                  Por: {item.usuario.nombre} {item.usuario.apellido}
                </Typography>
                
                {item.observaciones && (
                  <Typography variant="body2" sx={{ mt: 1, fontStyle: 'italic' }}>
                    "{item.observaciones}"
                  </Typography>
                )}
              </Paper>
            </TimelineContent>
          </TimelineItem>
        ))}
      </Timeline>
    </Box>
  );
};

const getColorEstado = (idEstado) => {
  const colors = {
    1: 'primary',
    2: 'warning',
    3: 'success',
    4: 'error'
  };
  return colors[idEstado] || 'grey';
};
```

---

## Flujo Completo de Usuario

### Escenario 1: Envío de Nota (CREADA → EN_TRANSITO)

1. Usuario ve listado de notas
2. Click en nota con estado "CREADA"
3. En el detalle, ve botón **"Enviar Nota"**
4. Click en "Enviar Nota"
5. Se abre diálogo pidiendo observaciones (opcional)
6. Usuario escribe: "Enviado en vehículo #3 con chofer Juan"
7. Click en "Confirmar"
8. Backend:
   - Cambia estado a EN_TRANSITO (2)
   - Registra fecha_envio
   - Registra id_usuario_envia
   - Envía email a tienda destino
   - Crea historial
9. Frontend muestra:
   - Estado actualizado a "EN TRÁNSITO" (chip naranja)
   - Fecha de envío y usuario
   - Timeline actualizado
   - Toast: "Estado actualizado exitosamente"

---

### Escenario 2: Recepción de Nota (EN_TRANSITO → RECIBIDA)

1. Usuario receptor ve listado de notas en tránsito
2. Click en nota "EN_TRANSITO"
3. Ve botón **"Confirmar Recepción"**
4. Click en "Confirmar Recepción"
5. Diálogo pide observaciones
6. Usuario escribe: "Recibido conforme, sin novedades"
7. Click en "Confirmar"
8. Backend:
   - Cambia estado a RECIBIDA (3)
   - Registra fecha_recepcion
   - Registra id_usuario_recibe
   - Envía email a tienda origen
   - Crea historial
9. Frontend muestra:
   - Estado "RECIBIDA" (chip verde)
   - Ya no muestra botones de acción
   - Alert: "Esta nota está en estado final"
   - Timeline completo

---

### Escenario 3: Cancelación (Cualquier estado → CANCELADA)

1. Usuario ve nota CREADA o EN_TRANSITO
2. Click en botón **"Cancelar Nota"** (rojo, outlined)
3. Diálogo de confirmación más estricto:
   - "¿Está seguro de cancelar esta nota?"
   - "Esta acción no se puede deshacer"
4. Usuario confirma y escribe razón: "Error en destino"
5. Backend cambia a CANCELADA (4)
6. Frontend:
   - Estado "CANCELADA" (chip rojo)
   - No permite más acciones
   - Historial actualizado

---

## Indicadores Visuales

### En el Listado de Notas

```tsx
const ListadoNotas = ({ notas }) => {
  return (
    <TableContainer>
      <Table>
        <TableHead>
          <TableRow>
            <TableCell>Número</TableCell>
            <TableCell>Tipo</TableCell>
            <TableCell>Origen</TableCell>
            <TableCell>Destino</TableCell>
            <TableCell>Estado</TableCell>
            <TableCell>Fecha</TableCell>
            <TableCell>Acciones</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          {notas.map(nota => (
            <TableRow key={nota.id_nota}>
              <TableCell>{nota.numero_nota}</TableCell>
              <TableCell>
                <Chip 
                  label={nota.tipo_nota} 
                  size="small"
                  color={nota.tipo_nota === 'ENTRADA' ? 'info' : 'secondary'}
                />
              </TableCell>
              <TableCell>{nota.tiendaOrigen?.nombre_tienda || nota.proveedor_origen}</TableCell>
              <TableCell>{nota.tiendaDestino?.nombre_tienda || nota.proveedor_destino}</TableCell>
              <TableCell>
                <EstadoChip estado={nota.estado} />
              </TableCell>
              <TableCell>{formatDate(nota.fecha_creacion)}</TableCell>
              <TableCell>
                <IconButton onClick={() => verDetalle(nota.id_nota)}>
                  <VisibilityIcon />
                </IconButton>
              </TableCell>
            </TableRow>
          ))}
        </TableBody>
      </Table>
    </TableContainer>
  );
};
```

---

## Validaciones y Restricciones

### Validar Transición de Estado (Frontend)

```tsx
const puedeTransicionar = (estadoActual, estadoNuevo) => {
  const transicionesPermitidas = {
    1: [2, 4], // CREADA puede ir a EN_TRANSITO o CANCELADA
    2: [3, 4], // EN_TRANSITO puede ir a RECIBIDA o CANCELADA
    3: [],     // RECIBIDA no puede cambiar
    4: []      // CANCELADA no puede cambiar
  };
  
  return transicionesPermitidas[estadoActual]?.includes(estadoNuevo) || false;
};

// Uso:
if (!puedeTransicionar(nota.id_estado, nuevoEstado)) {
  enqueueSnackbar('Transición de estado no permitida', { variant: 'error' });
  return;
}
```

---

## Notificaciones por Email (Automáticas)

El backend envía emails automáticamente en estos casos:

### Al Enviar Nota (Estado → EN_TRANSITO):
**Para:** Tienda destino / Proveedor destino  
**Asunto:** "Nueva nota en camino - {numero_nota}"  
**Contenido:**
- Número de nota
- Fecha de envío
- Artículos incluidos
- Vehículo y chofer
- Hora estimada de llegada

### Al Recibir Nota (Estado → RECIBIDA):
**Para:** Tienda origen / Proveedor origen  
**Asunto:** "Nota recibida - {numero_nota}"  
**Contenido:**
- Número de nota
- Fecha de recepción
- Usuario que recibió
- Observaciones

---

## Resumen de Implementación

### Checklist Frontend:

- [ ] Componente `EstadoChip` para mostrar estado visual
- [ ] Componente `AccionesNota` con botones condicionales
- [ ] Diálogo de confirmación para cambios de estado
- [ ] Función `cambiarEstadoNota()` con manejo de errores
- [ ] Componente `HistorialEstados` con Timeline
- [ ] Validación de transiciones permitidas
- [ ] Indicadores en listado de notas
- [ ] Filtros por estado en listado
- [ ] Manejo de permisos según usuario
- [ ] Tests de flujo completo

---

## Fecha de Documentación
3 de noviembre de 2025
