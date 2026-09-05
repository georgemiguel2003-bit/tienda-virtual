-- =============================================
-- BASE DE DATOS: tienda_virtual
-- Avance de Proyecto 2 - Programación Web Básica
-- =============================================

CREATE DATABASE IF NOT EXISTS tienda_virtual CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci;
USE tienda_virtual;

CREATE TABLE IF NOT EXISTS productos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(150)    NOT NULL,
    descripcion TEXT,
    precio      DECIMAL(10,2)   NOT NULL,
    stock       INT             NOT NULL DEFAULT 0,
    categoria   VARCHAR(80)     NOT NULL,
    marca       VARCHAR(80),
    imagen      VARCHAR(255)    DEFAULT 'img/default.jpg',
    created_at  TIMESTAMP       DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO productos (nombre, descripcion, precio, stock, categoria, marca, imagen) VALUES
('Laptop HP 15 Core i5',     'Laptop ideal para trabajo y estudio. Procesador Intel Core i5 de 11a generación, 8GB RAM, SSD 256GB, pantalla 15.6" Full HD.',        2899.90, 10, 'Laptops',    'HP',      'img/laptop_hp.jpg'),
('Laptop Lenovo IdeaPad 3',  'Diseño delgado y ligero. AMD Ryzen 5, 8GB RAM, SSD 512GB, pantalla 15.6" antirreflejo.',                                            2499.00,  7, 'Laptops',    'Lenovo',  'img/laptop_lenovo.jpg'),
('Smartphone Samsung A54',   'Pantalla Super AMOLED 6.4", cámara triple 50MP, batería 5000mAh, 128GB almacenamiento, resistencia IP67.',                           1299.00, 20, 'Celulares',  'Samsung', 'img/samsung_a54.jpg'),
('Smartphone Xiaomi Redmi 12','Pantalla 6.79" 90Hz, cámara 50MP, batería 5000mAh, carga rápida 33W, MIUI 14.',                                                      699.00, 35, 'Celulares',  'Xiaomi',  'img/xiaomi_redmi.jpg'),
('Auriculares Sony WH-1000XM5','Cancelación de ruido líder en la industria. 30h de batería, carga rápida, micrófono integrado, plegables.',                        1499.00,  5, 'Audio',      'Sony',    'img/sony_wh.jpg'),
('Teclado Mecánico Redragon K552','Teclado gaming compacto TKL. Switches red, retroiluminación RGB, construcción metálica robusta, anti-ghosting.',                229.90, 18, 'Periféricos','Redragon', 'img/teclado.jpg'),
('Monitor LG 24" Full HD',   'Panel IPS 24 pulgadas Full HD 1080p, tiempo de respuesta 5ms, HDMI + VGA, ajuste de inclinación.',                                   799.00,  8, 'Monitores',  'LG',      'img/monitor_lg.jpg'),
('Mouse Logitech MX Master 3','Mouse inalámbrico premium. Sensor 4000 DPI, scroll electromagnético, conectividad USB-C, hasta 70 días de batería.',                459.00, 12, 'Periféricos','Logitech', 'img/mouse_logitech.jpg');

