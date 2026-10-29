-- ============================================================
--  Base de datos: jorginho_uniatlantico
--  Generado automáticamente desde el código fuente
-- ============================================================

CREATE DATABASE IF NOT EXISTS `jorginho_uniatlantico`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `jorginho_uniatlantico`;

-- ------------------------------------------------------------
-- 1. usuarios
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id`         INT          NOT NULL AUTO_INCREMENT,
  `nombre`     VARCHAR(150) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario por defecto  →  email: admin@admin.com  |  contraseña: admin123
INSERT IGNORE INTO `usuarios` (`nombre`, `email`, `password`) VALUES
('Administrador', 'admin@admin.com', '$2y$10$cKabg/SawXziYFhX3X7qtOThpiZ5LbZacPCRZGKyHw8zcFpKs0tOO');

-- ------------------------------------------------------------
-- 2. encuestas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `encuestas` (
  `id`                   INT          NOT NULL AUTO_INCREMENT,
  `nombre`               VARCHAR(255) NOT NULL,
  `fecha_inicio`         DATETIME     NOT NULL,
  `fecha_cierre`         DATETIME     NOT NULL,
  `usuario_id`           INT          NOT NULL,
  `tipo_seguridad`       VARCHAR(30)  NOT NULL DEFAULT 'publica',
  `password`             VARCHAR(255)          DEFAULT NULL,
  `estado`               VARCHAR(20)  NOT NULL DEFAULT 'borrador',
  `banner_url`           TEXT                  DEFAULT NULL,
  `banner_modo`          VARCHAR(20)  NOT NULL DEFAULT 'enunciado',
  `background_url`       TEXT                  DEFAULT NULL,
  `opacidad_formulario`  INT          NOT NULL DEFAULT 100,
  `opacidad_banner`      INT          NOT NULL DEFAULT 100,
  `opacidad_sidebar`     INT          NOT NULL DEFAULT 100,
  `created_at`           TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_encuesta_usuario` (`usuario_id`),
  CONSTRAINT `fk_encuesta_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. encabezados_encuesta  (secciones)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `encabezados_encuesta` (
  `id`          INT          NOT NULL AUTO_INCREMENT,
  `encuesta_id` INT          NOT NULL,
  `titulo`      VARCHAR(255) NOT NULL,
  `descripcion` TEXT                  DEFAULT NULL,
  `orden`       INT          NOT NULL DEFAULT 0,
  `created_at`  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_encabezado_encuesta` (`encuesta_id`),
  CONSTRAINT `fk_encabezado_encuesta`
    FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. preguntas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `preguntas` (
  `id`                  INT          NOT NULL AUTO_INCREMENT,
  `encabezado_id`       INT          NOT NULL,
  `pregunta`            TEXT         NOT NULL,
  `tipo_pregunta`       VARCHAR(30)  NOT NULL DEFAULT 'texto_corto',
  `opciones`            LONGTEXT              DEFAULT NULL,
  `requerido`           TINYINT(1)   NOT NULL DEFAULT 1,
  `guardar_banco`       TINYINT(1)   NOT NULL DEFAULT 0,
  `orden`               INT          NOT NULL DEFAULT 0,
  `pregunta_padre_id`   INT                   DEFAULT NULL,
  `condicion_operador`  VARCHAR(20)           DEFAULT 'igual',
  `condicion_valor`     TEXT                  DEFAULT NULL,
  `created_at`          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_pregunta_encabezado` (`encabezado_id`),
  CONSTRAINT `fk_pregunta_encabezado`
    FOREIGN KEY (`encabezado_id`) REFERENCES `encabezados_encuesta` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. respuestas_encuesta
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `respuestas_encuesta` (
  `id`                INT          NOT NULL AUTO_INCREMENT,
  `encuesta_id`       INT          NOT NULL,
  `pregunta_id`       INT          NOT NULL,
  `respuesta`         LONGTEXT              DEFAULT NULL,
  `usuario_respondio` VARCHAR(255)          DEFAULT NULL,
  `ip_address`        VARCHAR(45)           DEFAULT NULL,
  `created_at`        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_resp_encuesta`  (`encuesta_id`),
  KEY `fk_resp_pregunta`  (`pregunta_id`),
  CONSTRAINT `fk_resp_encuesta`
    FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas`  (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_resp_pregunta`
    FOREIGN KEY (`pregunta_id`) REFERENCES `preguntas`  (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. banco_preguntas
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `banco_preguntas` (
  `id`            INT          NOT NULL AUTO_INCREMENT,
  `usuario_id`    INT          NOT NULL,
  `pregunta`      TEXT         NOT NULL,
  `tipo_pregunta` VARCHAR(30)  NOT NULL DEFAULT 'texto_corto',
  `opciones`      LONGTEXT              DEFAULT NULL,
  `categoría`     VARCHAR(100)          DEFAULT NULL,
  `veces_usado`   INT          NOT NULL DEFAULT 0,
  `created_at`    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_banco_usuario` (`usuario_id`),
  CONSTRAINT `fk_banco_usuario`
    FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. respuestas_acceso  (registro de accesos con gmail/password)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `respuestas_acceso` (
  `id`           INT          NOT NULL AUTO_INCREMENT,
  `encuesta_id`  INT          NOT NULL,
  `nombre`       VARCHAR(150)          DEFAULT NULL,
  `email`        VARCHAR(150)          DEFAULT NULL,
  `ip_address`   VARCHAR(45)           DEFAULT NULL,
  `tipo_acceso`  VARCHAR(30)  NOT NULL DEFAULT 'publica',
  `created_at`   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_acceso_encuesta` (`encuesta_id`),
  CONSTRAINT `fk_acceso_encuesta`
    FOREIGN KEY (`encuesta_id`) REFERENCES `encuestas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
