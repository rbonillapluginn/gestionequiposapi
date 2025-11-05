# 🚀 Sistema de Gestión de Equipos - API Backend

Sistema completo de APIs en Laravel para la gestión de notas de entrada y salida, control de inventario, y seguimiento de envíos entre tiendas.

## 📋 Características Principales

✅ **Autenticación con Laravel Sanctum** - Sistema robusto de tokens para API  
✅ **Gestión de Usuarios** - Con niveles de autorización y permisos granulares  
✅ **Notas de Movimiento** - Control completo de entradas y salidas  
✅ **Sistema de Permisos** - Control de acceso por secciones y tipos de movimiento  
✅ **Notificaciones por Email** - Alertas automáticas en cada etapa del proceso  
✅ **Dashboard de Monitoreo** - Vista general de envíos y recepciones  
✅ **Historial Completo** - Trazabilidad de todos los cambios de estado  
✅ **Múltiples Métodos de Envío** - Camión, mensajería interna (directa/recorrido), otros  
✅ **Validaciones Robustas** - Form Requests para validación de datos  
✅ **API RESTful Completa** - Siguiendo mejores prácticas  

## 🛠️ Requisitos

- PHP 8.2 o superior
- MySQL 5.7+ o MariaDB 10.3+
- Composer
- Laravel 11

## 📥 Instalación

### 1. Clonar o ubicar el proyecto

```bash
cd c:\laragon\www\gestionequiposapi
```

### 2. Instalar dependencias

```bash
composer install
```

### 3. Configurar archivo .env

Copia `.env.example` a `.env` y configura tu base de datos:

```env
APP_NAME="Sistema Gestión Equipos"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestionequipos
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Generar key de aplicación

```bash
php artisan key:generate
```

### 5. Ejecutar migraciones

```bash
php artisan migrate
```

### 6. Poblar base de datos con datos iniciales

```bash
php artisan db:seed
```

Esto creará:
- Estados de nota (CREADA, EN_TRANSITO, RECIBIDA, CANCELADA)
- Tipos de unidad de envío (CAJA, SOBRE, BULTO)
- Tipos de material (CARTON, PLASTICO)
- Métodos de envío (CAMION, MENSAJERIA_INTERNA, OTRO)
- Niveles de autorización
- Usuario administrador por defecto
- Secciones del sistema
- Plantillas de correo
- Y más...

### 7. Iniciar servidor de desarrollo

```bash
php artisan serve
```

La API estará disponible en: `http://localhost:8000/api`

## 🔐 Credenciales por Defecto

Después de ejecutar los seeders:

- **Username**: `admin`
- **Password**: `admin123`
- **Email**: `admin@sistema.com`

**⚠️ IMPORTANTE:** Cambia estas credenciales en producción.

## 📚 Documentación de la API

La documentación completa de todos los endpoints está disponible en el archivo:

👉 **[API_DOCUMENTATION.md](./API_DOCUMENTATION.md)**

## 🗂️ Estructura del Proyecto

```
gestionequiposapi/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── Api/
│   │   │       ├── AuthController.php
│   │   │       ├── UserController.php
│   │   │       ├── NotaMovimientoController.php
│   │   │       ├── ArticuloController.php
│   │   │       ├── TiendaController.php
│   │   │       ├── CatalogController.php
│   │   │       └── PermissionController.php
│   │   ├── Middleware/
│   │   │   └── CheckPermissions.php
│   │   └── Requests/
│   │       ├── StoreArticuloRequest.php
│   │       ├── StoreNotaMovimientoRequest.php
│   │       └── StoreUserRequest.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── NivelAutorizacion.php
│   │   ├── NotaMovimiento.php
│   │   ├── Articulo.php
│   │   ├── Tienda.php
│   │   └── ... (20+ modelos)
│   └── Services/
│       └── NotificationService.php
├── database/
│   ├── migrations/
│   │   ├── 2024_01_01_000001_create_usuarios_sistema_tables.php
│   │   ├── 2024_01_01_000002_create_tiendas_departamentos_tables.php
│   │   ├── 2024_01_01_000003_create_articulos_tables.php
│   │   ├── 2024_01_01_000004_create_unidades_envio_tables.php
│   │   ├── 2024_01_01_000005_create_metodos_envio_tables.php
│   │   ├── 2024_01_01_000006_create_notas_movimiento_tables.php
│   │   └── 2024_01_01_000007_create_notificaciones_monitoreo_tables.php
│   └── seeders/
│       ├── EstadoNotaSeeder.php
│       ├── TipoUnidadEnvioSeeder.php
│       ├── TipoMaterialSeeder.php
│       ├── MetodoEnvioSeeder.php
│       ├── InitialDataSeeder.php
│       └── DatabaseSeeder.php
├── routes/
│   └── api.php
└── DB/
    └── dbBase.sql (estructura de referencia)
```

## 🔄 Flujo Principal del Sistema

### 1. Autenticación
```
Login → Obtener Token → Usar Token en Headers
```

### 2. Crear Nota de Movimiento
```
POST /api/notas
↓
Estado: CREADA
↓
Envío de correo a destinatarios
```

### 3. Enviar Nota
```
PATCH /api/notas/{id}/status
Estado → EN_TRANSITO
↓
Registro de fecha_envio y usuario_envia
↓
Envío de correo de notificación
```

### 4. Recibir Nota
```
PATCH /api/notas/{id}/status
Estado → RECIBIDA
↓
Registro de fecha_recepcion y usuario_recibe
↓
Envío de correo de confirmación
```

### 5. Historial Completo
```
GET /api/notas/{id}/historial
↓
Ver todos los cambios de estado con usuarios y fechas
```

## 📊 Modelos Principales

### Tabla de Relaciones

| Modelo | Relaciones Principales |
|--------|----------------------|
| **User** | → NivelAutorizacion, EncargadosTienda, NotasCreadas/Enviadas/Recibidas |
| **NotaMovimiento** | → TipoMovimiento, TiendaOrigen/Destino, MetodoEnvio, Vehiculo, Chofer, Mensajero, Estado, DetallesArticulos |
| **Articulo** | → Categoria, DetallesNota |
| **Tienda** | → Encargados, NotasOrigen/Destino |
| **NivelAutorizacion** | → Usuarios, PermisosSecciones, PermisosTiposMovimiento |

## 🎯 Endpoints Más Importantes

### Autenticación
- `POST /api/auth/login` - Iniciar sesión
- `POST /api/auth/logout` - Cerrar sesión
- `GET /api/auth/me` - Usuario actual

### Notas de Movimiento
- `GET /api/notas` - Listar notas
- `POST /api/notas` - Crear nota
- `GET /api/notas/{id}` - Ver detalle
- `PATCH /api/notas/{id}/status` - Cambiar estado
- `GET /api/notas/dashboard` - Dashboard de monitoreo

### Artículos
- `GET /api/articulos` - Listar artículos
- `POST /api/articulos` - Crear artículo
- `POST /api/articulos/buscar-codigo` - Buscar por código/serie

### Catálogos
- `GET /api/catalogs` - Obtener todos los catálogos
- `GET /api/catalogs/{tipo}` - Catálogo específico

### Permisos
- `GET /api/permissions/mis-permisos` - Ver mis permisos
- `POST /api/permissions/verificar-permiso` - Verificar permiso específico

## 💡 Ejemplos de Uso

### Ejemplo: Crear y Enviar una Nota

```bash
# 1. Login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "admin123"
  }'

# Respuesta: { "token": "1|abc123..." }

# 2. Crear Nota
curl -X POST http://localhost:8000/api/notas \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "tipo_nota": "SALIDA",
    "id_tipo_movimiento": 1,
    "id_tienda_origen": 1,
    "id_tienda_destino": 2,
    "id_metodo_envio": 1,
    "id_vehiculo": 1,
    "id_chofer": 1,
    "articulos": [
      {
        "id_articulo": 1,
        "cantidad": 5
      }
    ]
  }'

# 3. Enviar Nota (cambiar estado)
curl -X PATCH http://localhost:8000/api/notas/1/status \
  -H "Authorization: Bearer 1|abc123..." \
  -H "Content-Type: application/json" \
  -d '{
    "id_estado": 2,
    "observaciones": "Enviado en camión #1"
  }'
```

## 📧 Configuración de Correos

Para que funcione el sistema de notificaciones por correo:

### Usando Gmail (recomendado para desarrollo)

1. Habilita "Acceso de aplicaciones menos seguras" o genera una "Contraseña de aplicación"
2. Configura en `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_email@gmail.com
MAIL_PASSWORD=tu_password_de_aplicacion
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_email@gmail.com
```

### Para Producción
Se recomienda usar servicios como:
- SendGrid
- Mailgun
- Amazon SES
- Postmark

## 🔒 Seguridad

- ✅ Autenticación con tokens (Laravel Sanctum)
- ✅ Middleware de autenticación en todas las rutas protegidas
- ✅ Sistema de permisos granular
- ✅ Validación de datos en todas las peticiones
- ✅ Hash de contraseñas con Bcrypt
- ✅ Protección CSRF habilitada
- ✅ Políticas de CORS configurables

## 🧪 Testing

```bash
# Ejecutar tests
php artisan test

# Con cobertura
php artisan test --coverage
```

## 🐛 Debugging

### Ver logs de la aplicación
```bash
tail -f storage/logs/laravel.log
```

### Limpiar caché
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

## 📈 Próximas Mejoras

- [ ] Implementar reportes en PDF
- [ ] Agregar gráficas y estadísticas avanzadas
- [ ] Sistema de notificaciones en tiempo real (WebSockets)
- [ ] API de exportación de datos (Excel, CSV)
- [ ] Integración con código de barras/QR
- [ ] App móvil para escaneo de artículos

## 🤝 Contribuir

Para contribuir al proyecto:

1. Fork el repositorio
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la licencia MIT.

## 👨‍💻 Desarrollo

**Desarrollado con:**
- Laravel 11
- PHP 8.2
- MySQL
- Laravel Sanctum
- Eloquent ORM

---

**¿Preguntas o problemas?** Abre un issue en el repositorio.

**Versión actual:** 1.0.0  
**Última actualización:** 30 de Octubre, 2024