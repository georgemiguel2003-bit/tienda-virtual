-- =============================================
-- AVANCE FINAL - Autenticacion + Carrito
-- Base de datos: tienda_virtual
-- Ejecutar DESPUES de tienda_virtual.sql
-- =============================================
USE tienda_virtual;

-- ---------------------------------------------
-- 1) TABLA DE USUARIOS (para el login)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(100) NOT NULL,
    correo     VARCHAR(120) NOT NULL UNIQUE,
    clave      VARCHAR(256) NOT NULL,            -- se guarda HASHEADA (SHA2-256)
    estado     TINYINT      NOT NULL DEFAULT 1,  -- 1 = activo, 0 = inactivo
    creado_en  TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario de prueba -> correo: demo@tienda.com   clave: 123456
INSERT INTO usuarios (nombre, correo, clave)
VALUES ('Jorge Demo', 'demo@tienda.com', SHA2('123456', 256))
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- ---------------------------------------------
-- 2) TABLAS DEL CARRITO
--    (tus servicios ya las usan, aqui quedan
--     formalizadas en el script)
-- ---------------------------------------------
CREATE TABLE IF NOT EXISTS carrito (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario          INT NOT NULL,
    fecha_creacion      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_carrito_usuario FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS carrito_detalle (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    carrito_id  INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad    INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_detalle_carrito  FOREIGN KEY (carrito_id)  REFERENCES carrito(id) ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES productos(id),
    CONSTRAINT uq_carrito_producto UNIQUE (carrito_id, producto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------
-- 3) PROCEDIMIENTO ALMACENADO DE VALIDACION (login)
--    El servicio web Login_validar.php INVOCA este SP.
-- ---------------------------------------------
DROP PROCEDURE IF EXISTS sp_validar_usuario;
DELIMITER //
CREATE PROCEDURE sp_validar_usuario(
    IN p_correo VARCHAR(120),
    IN p_clave  VARCHAR(256)
)
BEGIN
    SELECT id, nombre, correo
    FROM usuarios
    WHERE correo = p_correo
      AND clave  = SHA2(p_clave, 256)
      AND estado = 1;
END //
DELIMITER ;
