-- ============================================
-- PASO 1: Tablas independientes (sin FK)
-- ============================================

-- 1. Tabla de migraciones de Laravel
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Tablas de autenticación Laravel
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Cache
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Jobs
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PASO 2: Tablas de catálogos básicos
-- ============================================

-- 5. Niveles de autorización
CREATE TABLE IF NOT EXISTS `niveles_autorizacion` (
  `id_nivel` int NOT NULL AUTO_INCREMENT,
  `nombre_nivel` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `orden_jerarquico` int NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_nivel`),
  UNIQUE KEY `nombre_nivel` (`nombre_nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Secciones
CREATE TABLE IF NOT EXISTS `secciones` (
  `id_seccion` int NOT NULL AUTO_INCREMENT,
  `nombre_seccion` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `codigo_seccion` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_seccion`),
  UNIQUE KEY `nombre_seccion` (`nombre_seccion`),
  UNIQUE KEY `codigo_seccion` (`codigo_seccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Departamentos
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id_departamento` int NOT NULL AUTO_INCREMENT,
  `nombre_departamento` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_departamento` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_departamento`),
  UNIQUE KEY `codigo_departamento` (`codigo_departamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Tiendas
CREATE TABLE IF NOT EXISTS `tiendas` (
  `id_tienda` int NOT NULL AUTO_INCREMENT,
  `nombre_tienda` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_tienda` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_tienda`),
  UNIQUE KEY `codigo_tienda` (`codigo_tienda`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. Proveedores
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ruc` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `correo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `nombre_proveedor` (`nombre_proveedor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. Categorías de artículos
CREATE TABLE IF NOT EXISTS `categorias_articulos` (
  `id_categoria` int NOT NULL AUTO_INCREMENT,
  `nombre_categoria` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. Colores
CREATE TABLE IF NOT EXISTS `colores` (
  `id_color` int NOT NULL AUTO_INCREMENT,
  `nombre_color` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo_hex` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_color`),
  UNIQUE KEY `nombre_color` (`nombre_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. Tipos de material
CREATE TABLE IF NOT EXISTS `tipos_material` (
  `id_tipo_material` int NOT NULL AUTO_INCREMENT,
  `nombre_material` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requiere_color` tinyint(1) DEFAULT '0',
  `requiere_cintillo` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_tipo_material`),
  UNIQUE KEY `nombre_material` (`nombre_material`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. Tipos de unidad de envío
CREATE TABLE IF NOT EXISTS `tipos_unidad_envio` (
  `id_tipo_unidad` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_tipo_unidad`),
  UNIQUE KEY `nombre_tipo` (`nombre_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. Tipos de movimiento
CREATE TABLE IF NOT EXISTS `tipos_movimiento` (
  `id_tipo_movimiento` int NOT NULL AUTO_INCREMENT,
  `nombre_tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `codigo_tipo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_tipo_movimiento`),
  UNIQUE KEY `nombre_tipo` (`nombre_tipo`),
  UNIQUE KEY `codigo_tipo` (`codigo_tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. Estados de nota
CREATE TABLE IF NOT EXISTS `estados_nota` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `nombre_estado` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `orden` int NOT NULL,
  PRIMARY KEY (`id_estado`),
  UNIQUE KEY `nombre_estado` (`nombre_estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. Métodos de envío
CREATE TABLE IF NOT EXISTS `metodos_envio` (
  `id_metodo_envio` int NOT NULL AUTO_INCREMENT,
  `nombre_metodo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `requiere_vehiculo` tinyint(1) DEFAULT '0',
  `requiere_mensajero` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_metodo_envio`),
  UNIQUE KEY `nombre_metodo` (`nombre_metodo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. Choferes
CREATE TABLE IF NOT EXISTS `choferes` (
  `id_chofer` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `licencia` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_chofer`),
  UNIQUE KEY `licencia` (`licencia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. Mensajeros
CREATE TABLE IF NOT EXISTS `mensajeros` (
  `id_mensajero` int NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `identificacion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_mensajero`),
  UNIQUE KEY `identificacion` (`identificacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. Vehículos
CREATE TABLE IF NOT EXISTS `vehiculos` (
  `id_vehiculo` int NOT NULL AUTO_INCREMENT,
  `numero_camion` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `placa` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `capacidad_carga` decimal(10,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_vehiculo`),
  UNIQUE KEY `numero_camion` (`numero_camion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 20. Plantillas de correo
CREATE TABLE IF NOT EXISTS `plantillas_correo` (
  `id_plantilla` int NOT NULL AUTO_INCREMENT,
  `nombre_plantilla` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuerpo_html` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `cuerpo_texto` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_plantilla`),
  UNIQUE KEY `nombre_plantilla` (`nombre_plantilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PASO 3: Tablas con FK de nivel 1
-- ============================================

-- 21. Usuarios (depende de niveles_autorizacion)
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_nivel_autorizacion` int DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ultimo_login` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_usuarios_nivel` (`id_nivel_autorizacion`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_nivel_autorizacion`) REFERENCES `niveles_autorizacion` (`id_nivel`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 22. Artículos (depende de categorias_articulos)
CREATE TABLE IF NOT EXISTS `articulos` (
  `id_articulo` int NOT NULL AUTO_INCREMENT,
  `nombre_articulo` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `id_categoria` int DEFAULT NULL,
  `codigo_barra` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marca` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('disponible','en_uso','en_reparacion','dado_de_baja') COLLATE utf8mb4_unicode_ci DEFAULT 'disponible',
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `precio` decimal(10,2) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultima_modificacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_articulo`),
  UNIQUE KEY `unique_codigo_barra` (`codigo_barra`),
  UNIQUE KEY `unique_numero_serie` (`numero_serie`),
  KEY `idx_articulos_categoria` (`id_categoria`),
  CONSTRAINT `articulos_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categorias_articulos` (`id_categoria`),
  CONSTRAINT `articulos_chk_1` CHECK (((`codigo_barra` is not null) or (`numero_serie` is not null)))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 23. Unidades de envío (depende de tipos_unidad_envio, tipos_material, colores)
CREATE TABLE IF NOT EXISTS `unidades_envio` (
  `id_unidad_envio` int NOT NULL AUTO_INCREMENT,
  `id_tipo_unidad` int NOT NULL,
  `id_tipo_material` int DEFAULT NULL,
  `id_color` int DEFAULT NULL,
  `tiene_cintillo` tinyint(1) DEFAULT '0',
  `dimensiones` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peso_maximo` decimal(10,2) DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_unidad_envio`),
  KEY `id_tipo_unidad` (`id_tipo_unidad`),
  KEY `id_tipo_material` (`id_tipo_material`),
  KEY `id_color` (`id_color`),
  CONSTRAINT `unidades_envio_ibfk_1` FOREIGN KEY (`id_tipo_unidad`) REFERENCES `tipos_unidad_envio` (`id_tipo_unidad`),
  CONSTRAINT `unidades_envio_ibfk_2` FOREIGN KEY (`id_tipo_material`) REFERENCES `tipos_material` (`id_tipo_material`),
  CONSTRAINT `unidades_envio_ibfk_3` FOREIGN KEY (`id_color`) REFERENCES `colores` (`id_color`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 24. Submétodos de envío (depende de metodos_envio)
CREATE TABLE IF NOT EXISTS `submetodos_envio` (
  `id_submetodo` int NOT NULL AUTO_INCREMENT,
  `id_metodo_envio` int NOT NULL,
  `nombre_submetodo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `requiere_mensajero` tinyint(1) DEFAULT '0',
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_submetodo`),
  KEY `id_metodo_envio` (`id_metodo_envio`),
  CONSTRAINT `submetodos_envio_ibfk_1` FOREIGN KEY (`id_metodo_envio`) REFERENCES `metodos_envio` (`id_metodo_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 25. Encargados de tienda (depende de usuarios, tiendas, departamentos)
CREATE TABLE IF NOT EXISTS `encargados_tienda` (
  `id_encargado` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_tienda` int NOT NULL,
  `id_departamento` int NOT NULL,
  `es_principal` tinyint(1) DEFAULT '0',
  `fecha_asignacion` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id_encargado`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_departamento` (`id_departamento`),
  KEY `idx_encargados_tienda` (`id_tienda`,`id_departamento`),
  CONSTRAINT `encargados_tienda_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `encargados_tienda_ibfk_2` FOREIGN KEY (`id_tienda`) REFERENCES `tiendas` (`id_tienda`),
  CONSTRAINT `encargados_tienda_ibfk_3` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 26. Permisos de secciones (depende de niveles_autorizacion, secciones)
CREATE TABLE IF NOT EXISTS `permisos_secciones` (
  `id_permiso_seccion` int NOT NULL AUTO_INCREMENT,
  `id_nivel_autorizacion` int NOT NULL,
  `id_seccion` int NOT NULL,
  `puede_leer` tinyint(1) DEFAULT '0',
  `puede_crear` tinyint(1) DEFAULT '0',
  `puede_modificar` tinyint(1) DEFAULT '0',
  `puede_eliminar` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_permiso_seccion`),
  UNIQUE KEY `unique_nivel_seccion` (`id_nivel_autorizacion`,`id_seccion`),
  KEY `id_seccion` (`id_seccion`),
  CONSTRAINT `permisos_secciones_ibfk_1` FOREIGN KEY (`id_nivel_autorizacion`) REFERENCES `niveles_autorizacion` (`id_nivel`),
  CONSTRAINT `permisos_secciones_ibfk_2` FOREIGN KEY (`id_seccion`) REFERENCES `secciones` (`id_seccion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 27. Permisos de tipos de movimiento (depende de niveles_autorizacion, tipos_movimiento)
CREATE TABLE IF NOT EXISTS `permisos_tipos_movimiento` (
  `id_permiso_movimiento` int NOT NULL AUTO_INCREMENT,
  `id_nivel_autorizacion` int NOT NULL,
  `id_tipo_movimiento` int NOT NULL,
  `puede_ejecutar` tinyint(1) DEFAULT '0',
  `requiere_autorizacion` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_permiso_movimiento`),
  UNIQUE KEY `unique_nivel_tipo` (`id_nivel_autorizacion`,`id_tipo_movimiento`),
  KEY `id_tipo_movimiento` (`id_tipo_movimiento`),
  CONSTRAINT `permisos_tipos_movimiento_ibfk_1` FOREIGN KEY (`id_nivel_autorizacion`) REFERENCES `niveles_autorizacion` (`id_nivel`),
  CONSTRAINT `permisos_tipos_movimiento_ibfk_2` FOREIGN KEY (`id_tipo_movimiento`) REFERENCES `tipos_movimiento` (`id_tipo_movimiento`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PASO 4: Tabla notas_movimiento (nivel 2)
-- ============================================

-- 28. Notas de movimiento (depende de muchas tablas)
CREATE TABLE IF NOT EXISTS `notas_movimiento` (
  `id_nota` int NOT NULL AUTO_INCREMENT,
  `numero_nota` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_nota` enum('ENTRADA','SALIDA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_tipo_movimiento` int NOT NULL,
  `id_tienda_origen` int DEFAULT NULL,
  `id_tienda_destino` int DEFAULT NULL,
  `id_proveedor_destino` int DEFAULT NULL,
  `id_metodo_envio` int NOT NULL,
  `id_submetodo_envio` int DEFAULT NULL,
  `id_vehiculo` int DEFAULT NULL,
  `id_chofer` int DEFAULT NULL,
  `hora_salida` datetime DEFAULT NULL,
  `id_mensajero` int DEFAULT NULL,
  `fecha_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_envio` datetime DEFAULT NULL,
  `fecha_recepcion` datetime DEFAULT NULL,
  `id_usuario_crea` int NOT NULL,
  `id_usuario_envia` int DEFAULT NULL,
  `id_usuario_recibe` int DEFAULT NULL,
  `id_estado` int NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_nota`),
  UNIQUE KEY `numero_nota` (`numero_nota`),
  KEY `id_tipo_movimiento` (`id_tipo_movimiento`),
  KEY `id_tienda_destino` (`id_tienda_destino`),
  KEY `id_proveedor_destino` (`id_proveedor_destino`),
  KEY `id_metodo_envio` (`id_metodo_envio`),
  KEY `id_submetodo_envio` (`id_submetodo_envio`),
  KEY `id_vehiculo` (`id_vehiculo`),
  KEY `id_chofer` (`id_chofer`),
  KEY `id_mensajero` (`id_mensajero`),
  KEY `id_usuario_crea` (`id_usuario_crea`),
  KEY `id_usuario_envia` (`id_usuario_envia`),
  KEY `id_usuario_recibe` (`id_usuario_recibe`),
  KEY `idx_notas_fechas` (`fecha_creacion`,`fecha_envio`,`fecha_recepcion`),
  KEY `idx_notas_estado` (`id_estado`),
  KEY `idx_notas_tiendas` (`id_tienda_origen`,`id_tienda_destino`),
  CONSTRAINT `notas_movimiento_ibfk_1` FOREIGN KEY (`id_tipo_movimiento`) REFERENCES `tipos_movimiento` (`id_tipo_movimiento`),
  CONSTRAINT `notas_movimiento_ibfk_10` FOREIGN KEY (`id_usuario_envia`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `notas_movimiento_ibfk_11` FOREIGN KEY (`id_usuario_recibe`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `notas_movimiento_ibfk_12` FOREIGN KEY (`id_estado`) REFERENCES `estados_nota` (`id_estado`),
  CONSTRAINT `notas_movimiento_ibfk_13` FOREIGN KEY (`id_proveedor_destino`) REFERENCES `proveedores` (`id_proveedor`),
  CONSTRAINT `notas_movimiento_ibfk_2` FOREIGN KEY (`id_tienda_origen`) REFERENCES `tiendas` (`id_tienda`),
  CONSTRAINT `notas_movimiento_ibfk_3` FOREIGN KEY (`id_tienda_destino`) REFERENCES `tiendas` (`id_tienda`),
  CONSTRAINT `notas_movimiento_ibfk_4` FOREIGN KEY (`id_metodo_envio`) REFERENCES `metodos_envio` (`id_metodo_envio`),
  CONSTRAINT `notas_movimiento_ibfk_5` FOREIGN KEY (`id_submetodo_envio`) REFERENCES `submetodos_envio` (`id_submetodo`),
  CONSTRAINT `notas_movimiento_ibfk_6` FOREIGN KEY (`id_vehiculo`) REFERENCES `vehiculos` (`id_vehiculo`),
  CONSTRAINT `notas_movimiento_ibfk_7` FOREIGN KEY (`id_chofer`) REFERENCES `choferes` (`id_chofer`),
  CONSTRAINT `notas_movimiento_ibfk_8` FOREIGN KEY (`id_mensajero`) REFERENCES `mensajeros` (`id_mensajero`),
  CONSTRAINT `notas_movimiento_ibfk_9` FOREIGN KEY (`id_usuario_crea`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `notas_movimiento_chk_1` CHECK (((`id_tienda_origen` is not null) and ((`id_tienda_destino` is not null) or (`id_proveedor_destino` is not null))))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- PASO 5: Tablas dependientes de notas_movimiento (nivel 3)
-- ============================================

-- 29. Detalle de nota artículos (depende de notas_movimiento, articulos, unidades_envio)
CREATE TABLE IF NOT EXISTS `detalle_nota_articulos` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_nota` int NOT NULL,
  `id_articulo` int NOT NULL,
  `cantidad` int NOT NULL,
  `id_unidad_envio` int DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_detalle`),
  KEY `id_articulo` (`id_articulo`),
  KEY `id_unidad_envio` (`id_unidad_envio`),
  KEY `idx_detalle_nota` (`id_nota`),
  CONSTRAINT `detalle_nota_articulos_ibfk_1` FOREIGN KEY (`id_nota`) REFERENCES `notas_movimiento` (`id_nota`),
  CONSTRAINT `detalle_nota_articulos_ibfk_2` FOREIGN KEY (`id_articulo`) REFERENCES `articulos` (`id_articulo`),
  CONSTRAINT `detalle_nota_articulos_ibfk_3` FOREIGN KEY (`id_unidad_envio`) REFERENCES `unidades_envio` (`id_unidad_envio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 30. Historial de estados de nota (depende de notas_movimiento, estados_nota, usuarios)
CREATE TABLE IF NOT EXISTS `historial_estados_nota` (
  `id_historial` int NOT NULL AUTO_INCREMENT,
  `id_nota` int NOT NULL,
  `id_estado_anterior` int DEFAULT NULL,
  `id_estado_nuevo` int NOT NULL,
  `id_usuario` int NOT NULL,
  `fecha_cambio` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_historial`),
  KEY `id_estado_anterior` (`id_estado_anterior`),
  KEY `id_estado_nuevo` (`id_estado_nuevo`),
  KEY `id_usuario` (`id_usuario`),
  KEY `idx_historial_nota` (`id_nota`),
  CONSTRAINT `historial_estados_nota_ibfk_1` FOREIGN KEY (`id_nota`) REFERENCES `notas_movimiento` (`id_nota`),
  CONSTRAINT `historial_estados_nota_ibfk_2` FOREIGN KEY (`id_estado_anterior`) REFERENCES `estados_nota` (`id_estado`),
  CONSTRAINT `historial_estados_nota_ibfk_3` FOREIGN KEY (`id_estado_nuevo`) REFERENCES `estados_nota` (`id_estado`),
  CONSTRAINT `historial_estados_nota_ibfk_4` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 31. Firmas digitales (depende de notas_movimiento)
CREATE TABLE IF NOT EXISTS `firmas_digitales` (
  `id_firma` int NOT NULL AUTO_INCREMENT,
  `id_nota` int NOT NULL COMMENT 'FK a notas_movimiento',
  `nombre_completo_firmante` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cedula_firmante` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cargo_firmante` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `firma_base64` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Imagen de firma en base64',
  `tipo_firma` enum('despacho','recepcion') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'despacho',
  `estado_anterior` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_nuevo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_firma` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_firma`),
  KEY `idx_nota` (`id_nota`),
  KEY `idx_tipo_firma` (`tipo_firma`),
  KEY `idx_fecha_firma` (`fecha_firma`),
  CONSTRAINT `firmas_digitales_ibfk_1` FOREIGN KEY (`id_nota`) REFERENCES `notas_movimiento` (`id_nota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 32. Log de correos (depende de notas_movimiento, plantillas_correo)
CREATE TABLE IF NOT EXISTS `log_correos` (
  `id_log_correo` int NOT NULL AUTO_INCREMENT,
  `id_nota` int NOT NULL,
  `id_plantilla` int NOT NULL,
  `destinatarios` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `asunto` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `enviado` tinyint(1) DEFAULT '0',
  `fecha_envio` timestamp NULL DEFAULT NULL,
  `error` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id_log_correo`),
  KEY `id_nota` (`id_nota`),
  KEY `id_plantilla` (`id_plantilla`),
  CONSTRAINT `log_correos_ibfk_1` FOREIGN KEY (`id_nota`) REFERENCES `notas_movimiento` (`id_nota`),
  CONSTRAINT `log_correos_ibfk_2` FOREIGN KEY (`id_plantilla`) REFERENCES `plantillas_correo` (`id_plantilla`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 33. Permisos de procesos de usuario (depende de usuarios, estados_nota)
CREATE TABLE IF NOT EXISTS `permisos_procesos_usuario` (
  `id_permiso_proceso` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `id_estado` int NOT NULL,
  `tiene_permiso` tinyint(1) NOT NULL DEFAULT '1',
  `fecha_asignacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_usuario_asigna` int DEFAULT NULL,
  PRIMARY KEY (`id_permiso_proceso`),
  UNIQUE KEY `idx_usuario_estado_proceso` (`id_usuario`,`id_estado`),
  KEY `idx_permisos_proceso_usuario` (`id_usuario`),
  KEY `idx_permisos_proceso_estado` (`id_estado`),
  KEY `idx_permisos_proceso_activo` (`tiene_permiso`),
  KEY `id_usuario_asigna` (`id_usuario_asigna`),
  CONSTRAINT `permisos_procesos_usuario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `permisos_procesos_usuario_ibfk_2` FOREIGN KEY (`id_estado`) REFERENCES `estados_nota` (`id_estado`) ON DELETE CASCADE,
  CONSTRAINT `permisos_procesos_usuario_ibfk_3` FOREIGN KEY (`id_usuario_asigna`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;