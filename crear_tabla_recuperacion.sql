-- Script SQL para crear la tabla de recuperación de contraseña
-- Ejecuta este script en tu base de datos MySQL

CREATE TABLE IF NOT EXISTS `recuperacion_password` (
  `id_recuperacion` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_expiracion` datetime NOT NULL,
  `usado` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id_recuperacion`),
  UNIQUE KEY `token_UNIQUE` (`token`),
  KEY `id_usuario_fk_idx` (`id_usuario`),
  KEY `token_idx` (`token`),
  CONSTRAINT `id_usuario_recuperacion_fk` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

