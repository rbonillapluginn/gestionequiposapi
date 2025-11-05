# Sistema de Gestión de Equipos y Logística

API REST desarrollada con Laravel 12 para la gestión integral de movimientos de equipos, artículos e inventario entre tiendas y proveedores.

## 📋 Características Principales

- **Gestión de Notas de Movimiento**: Control completo de entradas y salidas de equipos
- **Workflow de Estados**: CREADA → EN_TRANSITO → RECIBIDA, con trazabilidad completa
- **Firmas Digitales**: Sistema de doble firma (despacho y recepción) con captura de datos del firmante
- **Notificaciones por Email**: Alertas automáticas en cada cambio de estado
- **Sistema de Permisos Multinivel**:
  - Permisos por secciones (según nivel de autorización)
  - Permisos por tipos de movimiento
  - Permisos de procesos individuales por usuario
- **Gestión de Proveedores**: Soporte para envíos a proveedores externos
- **Unidades de Envío Dinámicas**: Creación automática de configuraciones de empaque
- **Multi-Tienda**: Gestión de múltiples tiendas con encargados por departamento

## 🛠️ Tecnologías

- **Laravel 12** (PHP 8.2+)
- **MySQL** - Base de datos relacional (28+ tablas)
- **Laravel Sanctum** - Autenticación por tokens
- **SMTP Gmail** - Sistema de notificaciones
- **Español** - Internacionalización completa

## 📦 Instalación

### Requisitos Previos
- PHP >= 8.2
- Composer
- MySQL
- Servidor web (Apache/Nginx)
