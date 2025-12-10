# Sistema de Gestión de Equipos y Logística

API REST desarrollada con Laravel 12 para la gestión integral de movimientos de equipos, artículos e inventario entre tiendas y devolucion a proveedores.

## Características Principales

- **Gestión de Notas de Movimiento**: Control completo de entradas y salidas de equipos
- **Estados de Movimiento**: CREADA → EN_TRANSITO → RECIBIDA, con trazabilidad completa
- **Firmas Digitales**: Sistema de doble firma (despacho y recepción) con captura de datos del firmante
- **Notificaciones por Email**: Alertas automáticas en cada cambio de estado
- **Sistema de Permisos Multinivel**:
  - Permisos por secciones (según nivel de autorización)
  - Permisos por tipos de movimiento
  - Permisos de procesos individuales por usuario
- **Gestión de Proveedores**: Soporte para envíos a proveedores externos
- **Multi-Tienda**: Gestión de múltiples tiendas con encargados por departamento

