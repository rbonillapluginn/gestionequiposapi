-- =============================================
-- SISTEMA DE LOGÍSTICA E INVENTARIO - MySQL
-- Base de Datos Completa
-- =============================================

-- =============================================
-- MÓDULO DE AUTENTICACIÓN Y USUARIOS
-- =============================================

-- Niveles de Autorización (primero porque usuarios depende de esta)
CREATE TABLE niveles_autorizacion (
    id_nivel INT AUTO_INCREMENT PRIMARY KEY,
    nombre_nivel VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    orden_jerarquico INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de Usuarios
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    id_nivel_autorizacion INT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    ultimo_login TIMESTAMP NULL,
    FOREIGN KEY (id_nivel_autorizacion) REFERENCES niveles_autorizacion(id_nivel)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Secciones del Sistema
CREATE TABLE secciones (
    id_seccion INT AUTO_INCREMENT PRIMARY KEY,
    nombre_seccion VARCHAR(100) UNIQUE NOT NULL,
    descripcion TEXT,
    codigo_seccion VARCHAR(20) UNIQUE NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos para Acceso a Secciones
CREATE TABLE permisos_secciones (
    id_permiso_seccion INT AUTO_INCREMENT PRIMARY KEY,
    id_nivel_autorizacion INT NOT NULL,
    id_seccion INT NOT NULL,
    puede_leer TINYINT(1) DEFAULT 0,
    puede_crear TINYINT(1) DEFAULT 0,
    puede_modificar TINYINT(1) DEFAULT 0,
    puede_eliminar TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_nivel_autorizacion) REFERENCES niveles_autorizacion(id_nivel),
    FOREIGN KEY (id_seccion) REFERENCES secciones(id_seccion),
    UNIQUE KEY unique_nivel_seccion (id_nivel_autorizacion, id_seccion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de Movimiento
CREATE TABLE tipos_movimiento (
    id_tipo_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipo VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    codigo_tipo VARCHAR(20) UNIQUE NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Permisos para Tipos de Movimiento
CREATE TABLE permisos_tipos_movimiento (
    id_permiso_movimiento INT AUTO_INCREMENT PRIMARY KEY,
    id_nivel_autorizacion INT NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    puede_ejecutar TINYINT(1) DEFAULT 0,
    requiere_autorizacion TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_nivel_autorizacion) REFERENCES niveles_autorizacion(id_nivel),
    FOREIGN KEY (id_tipo_movimiento) REFERENCES tipos_movimiento(id_tipo_movimiento),
    UNIQUE KEY unique_nivel_tipo (id_nivel_autorizacion, id_tipo_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE TIENDAS Y DEPARTAMENTOS
-- =============================================

-- Departamentos
CREATE TABLE departamentos (
    id_departamento INT AUTO_INCREMENT PRIMARY KEY,
    nombre_departamento VARCHAR(100) NOT NULL,
    codigo_departamento VARCHAR(20) UNIQUE NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tiendas
CREATE TABLE tiendas (
    id_tienda INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tienda VARCHAR(100) NOT NULL,
    codigo_tienda VARCHAR(20) UNIQUE NOT NULL,
    direccion TEXT,
    telefono VARCHAR(20),
    email VARCHAR(100),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Encargados de Tienda por Departamento
CREATE TABLE encargados_tienda (
    id_encargado INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_tienda INT NOT NULL,
    id_departamento INT NOT NULL,
    es_principal TINYINT(1) DEFAULT 0,
    fecha_asignacion DATE NOT NULL,
    fecha_fin DATE NULL,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_tienda) REFERENCES tiendas(id_tienda),
    FOREIGN KEY (id_departamento) REFERENCES departamentos(id_departamento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE ARTÍCULOS
-- =============================================

-- Categorías de Artículos
CREATE TABLE categorias_articulos (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Artículos (Código de Barra o Número de Serie obligatorio)
CREATE TABLE articulos (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    nombre_articulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    id_categoria INT,
    codigo_barra VARCHAR(100) NULL,
    numero_serie VARCHAR(100) NULL,
    precio DECIMAL(10,2),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_ultima_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_categoria) REFERENCES categorias_articulos(id_categoria),
    UNIQUE KEY unique_codigo_barra (codigo_barra),
    UNIQUE KEY unique_numero_serie (numero_serie),
    CHECK (codigo_barra IS NOT NULL OR numero_serie IS NOT NULL)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE UNIDADES DE ENVÍO
-- =============================================

-- Tabla de Colores
CREATE TABLE colores (
    id_color INT AUTO_INCREMENT PRIMARY KEY,
    nombre_color VARCHAR(50) UNIQUE NOT NULL,
    codigo_hex VARCHAR(7),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de Unidad de Envío (Caja, Sobre, Bulto)
CREATE TABLE tipos_unidad_envio (
    id_tipo_unidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre_tipo VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tipos de Material (para Cajas: Cartón, Plástico)
CREATE TABLE tipos_material (
    id_tipo_material INT AUTO_INCREMENT PRIMARY KEY,
    nombre_material VARCHAR(50) UNIQUE NOT NULL,
    requiere_color TINYINT(1) DEFAULT 0,
    requiere_cintillo TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Unidades de Envío
CREATE TABLE unidades_envio (
    id_unidad_envio INT AUTO_INCREMENT PRIMARY KEY,
    id_tipo_unidad INT NOT NULL,
    id_tipo_material INT NULL,
    id_color INT NULL,
    tiene_cintillo TINYINT(1) DEFAULT 0,
    dimensiones VARCHAR(50),
    peso_maximo DECIMAL(10,2),
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_tipo_unidad) REFERENCES tipos_unidad_envio(id_tipo_unidad),
    FOREIGN KEY (id_tipo_material) REFERENCES tipos_material(id_tipo_material),
    FOREIGN KEY (id_color) REFERENCES colores(id_color)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE MÉTODOS DE ENVÍO
-- =============================================

-- Métodos de Envío (Camión, Mensajería Interna, Otro)
CREATE TABLE metodos_envio (
    id_metodo_envio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_metodo VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    requiere_vehiculo TINYINT(1) DEFAULT 0,
    requiere_mensajero TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sub-métodos de Envío (para Mensajería: Directo, Recorrido)
CREATE TABLE submetodos_envio (
    id_submetodo INT AUTO_INCREMENT PRIMARY KEY,
    id_metodo_envio INT NOT NULL,
    nombre_submetodo VARCHAR(50) NOT NULL,
    descripcion TEXT,
    requiere_mensajero TINYINT(1) DEFAULT 0,
    activo TINYINT(1) DEFAULT 1,
    FOREIGN KEY (id_metodo_envio) REFERENCES metodos_envio(id_metodo_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Vehículos (Camiones)
CREATE TABLE vehiculos (
    id_vehiculo INT AUTO_INCREMENT PRIMARY KEY,
    numero_camion VARCHAR(50) UNIQUE NOT NULL,
    placa VARCHAR(20),
    modelo VARCHAR(100),
    capacidad_carga DECIMAL(10,2),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Choferes
CREATE TABLE choferes (
    id_chofer INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(200) NOT NULL,
    licencia VARCHAR(50) UNIQUE NOT NULL,
    telefono VARCHAR(20),
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Mensajeros
CREATE TABLE mensajeros (
    id_mensajero INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(200) NOT NULL,
    telefono VARCHAR(20),
    identificacion VARCHAR(50) UNIQUE NOT NULL,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE NOTAS DE ENTRADA Y SALIDA
-- =============================================

-- Estados de Nota
CREATE TABLE estados_nota (
    id_estado INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estado VARCHAR(50) UNIQUE NOT NULL,
    descripcion TEXT,
    orden INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notas de Movimiento (Entrada y Salida)
CREATE TABLE notas_movimiento (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    numero_nota VARCHAR(50) UNIQUE NOT NULL,
    tipo_nota ENUM('ENTRADA', 'SALIDA') NOT NULL,
    id_tipo_movimiento INT NOT NULL,
    
    -- Origen y Destino
    id_tienda_origen INT NULL,
    id_tienda_destino INT NULL,
    proveedor_origen VARCHAR(200) NULL,
    proveedor_destino VARCHAR(200) NULL,
    
    -- Método de Envío
    id_metodo_envio INT NOT NULL,
    id_submetodo_envio INT NULL,
    
    -- Detalles de Transporte
    id_vehiculo INT NULL,
    id_chofer INT NULL,
    hora_salida DATETIME NULL,
    id_mensajero INT NULL,
    
    -- Información General
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_envio DATETIME NULL,
    fecha_recepcion DATETIME NULL,
    id_usuario_crea INT NOT NULL,
    id_usuario_envia INT NULL,
    id_usuario_recibe INT NULL,
    
    -- Estado y Observaciones
    id_estado INT NOT NULL,
    observaciones TEXT,
    
    FOREIGN KEY (id_tipo_movimiento) REFERENCES tipos_movimiento(id_tipo_movimiento),
    FOREIGN KEY (id_tienda_origen) REFERENCES tiendas(id_tienda),
    FOREIGN KEY (id_tienda_destino) REFERENCES tiendas(id_tienda),
    FOREIGN KEY (id_metodo_envio) REFERENCES metodos_envio(id_metodo_envio),
    FOREIGN KEY (id_submetodo_envio) REFERENCES submetodos_envio(id_submetodo),
    FOREIGN KEY (id_vehiculo) REFERENCES vehiculos(id_vehiculo),
    FOREIGN KEY (id_chofer) REFERENCES choferes(id_chofer),
    FOREIGN KEY (id_mensajero) REFERENCES mensajeros(id_mensajero),
    FOREIGN KEY (id_usuario_crea) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_usuario_envia) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_usuario_recibe) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_estado) REFERENCES estados_nota(id_estado),
    CHECK (
        (id_tienda_origen IS NOT NULL OR proveedor_origen IS NOT NULL) AND
        (id_tienda_destino IS NOT NULL OR proveedor_destino IS NOT NULL)
    )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Detalle de Artículos en Notas
CREATE TABLE detalle_nota_articulos (
    id_detalle INT AUTO_INCREMENT PRIMARY KEY,
    id_nota INT NOT NULL,
    id_articulo INT NOT NULL,
    cantidad INT NOT NULL,
    id_unidad_envio INT NULL,
    observaciones TEXT,
    FOREIGN KEY (id_nota) REFERENCES notas_movimiento(id_nota),
    FOREIGN KEY (id_articulo) REFERENCES articulos(id_articulo),
    FOREIGN KEY (id_unidad_envio) REFERENCES unidades_envio(id_unidad_envio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE NOTIFICACIONES Y CORREOS
-- =============================================

-- Plantillas de Correo
CREATE TABLE plantillas_correo (
    id_plantilla INT AUTO_INCREMENT PRIMARY KEY,
    nombre_plantilla VARCHAR(100) UNIQUE NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    cuerpo_html TEXT NOT NULL,
    cuerpo_texto TEXT,
    activo TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Log de Correos Enviados
CREATE TABLE log_correos (
    id_log_correo INT AUTO_INCREMENT PRIMARY KEY,
    id_nota INT NOT NULL,
    id_plantilla INT NOT NULL,
    destinatarios TEXT NOT NULL,
    asunto VARCHAR(200) NOT NULL,
    enviado TINYINT(1) DEFAULT 0,
    fecha_envio TIMESTAMP NULL,
    error TEXT NULL,
    FOREIGN KEY (id_nota) REFERENCES notas_movimiento(id_nota),
    FOREIGN KEY (id_plantilla) REFERENCES plantillas_correo(id_plantilla)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- MÓDULO DE MONITOREO
-- =============================================

-- Historial de Cambios de Estado
CREATE TABLE historial_estados_nota (
    id_historial INT AUTO_INCREMENT PRIMARY KEY,
    id_nota INT NOT NULL,
    id_estado_anterior INT NULL,
    id_estado_nuevo INT NOT NULL,
    id_usuario INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    observaciones TEXT,
    FOREIGN KEY (id_nota) REFERENCES notas_movimiento(id_nota),
    FOREIGN KEY (id_estado_anterior) REFERENCES estados_nota(id_estado),
    FOREIGN KEY (id_estado_nuevo) REFERENCES estados_nota(id_estado),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- ÍNDICES ADICIONALES PARA OPTIMIZACIÓN
-- =============================================

CREATE INDEX idx_usuarios_nivel ON usuarios(id_nivel_autorizacion);
CREATE INDEX idx_encargados_tienda ON encargados_tienda(id_tienda, id_departamento);
CREATE INDEX idx_articulos_categoria ON articulos(id_categoria);
CREATE INDEX idx_notas_fechas ON notas_movimiento(fecha_creacion, fecha_envio, fecha_recepcion);
CREATE INDEX idx_notas_estado ON notas_movimiento(id_estado);
CREATE INDEX idx_notas_tiendas ON notas_movimiento(id_tienda_origen, id_tienda_destino);
CREATE INDEX idx_detalle_nota ON detalle_nota_articulos(id_nota);
CREATE INDEX idx_historial_nota ON historial_estados_nota(id_nota);

-- =============================================
-- DATOS INICIALES
-- =============================================

-- Estados de Nota
INSERT INTO estados_nota (nombre_estado, descripcion, orden) VALUES
('CREADA', 'Nota creada pero no enviada', 1),
('EN_TRANSITO', 'Nota enviada, en camino', 2),
('RECIBIDA', 'Nota recibida en destino', 3),
('CANCELADA', 'Nota cancelada', 4);

-- Tipos de Unidad de Envío
INSERT INTO tipos_unidad_envio (nombre_tipo, descripcion) VALUES
('CAJA', 'Caja para empaque'),
('SOBRE', 'Sobre de documentos o artículos pequeños'),
('BULTO', 'Bulto general');

-- Tipos de Material
INSERT INTO tipos_material (nombre_material, requiere_color, requiere_cintillo) VALUES
('CARTON', 0, 0),
('PLASTICO', 1, 1);

-- Métodos de Envío
INSERT INTO metodos_envio (nombre_metodo, descripcion, requiere_vehiculo, requiere_mensajero) VALUES
('CAMION', 'Envío por camión de la empresa', 1, 0),
('MENSAJERIA_INTERNA', 'Mensajería interna de la empresa', 0, 1),
('OTRO', 'Otro método de envío', 0, 0);

-- Submétodos de Envío para Mensajería
INSERT INTO submetodos_envio (id_metodo_envio, nombre_submetodo, descripcion, requiere_mensajero) VALUES
(2, 'DIRECTO', 'Entrega directa al destino', 1),
(2, 'RECORRIDO', 'Entrega por recorrido con múltiples paradas', 1);