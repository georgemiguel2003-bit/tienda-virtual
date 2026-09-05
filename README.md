# Tienda Virtual — E-commerce con PHP y MySQL

Proyecto académico de una tienda virtual completa, desarrollado como parte de la carrera técnica de Desarrollo de Software en ISIL. Implementa el flujo típico de un e-commerce: catálogo, búsqueda con filtros, autenticación de usuarios, carrito de compras y persistencia en base de datos relacional.

## Funcionalidades

- Catálogo de productos con búsqueda avanzada y filtros dinámicos (nombre, categoría, precio).
- Vista de detalle de producto: imagen, stock disponible y productos relacionados.
- Autenticación de usuarios: registro, login, logout y manejo de sesión.
- Carrito de compras: agregar, listar, actualizar y eliminar productos.
- Diseño responsive con HTML5 y CSS3.

## Tecnologías

- **Backend:** PHP
- **Base de datos:** MySQL (consultas con PDO y prepared statements)
- **Frontend:** HTML5, CSS3, JavaScript
- **Entorno local:** XAMPP (Apache + MySQL)

## Estructura del proyecto
- img/ # Imágenes de productos
- index.php # Página principal / catálogo
- detalle.php # Vista de detalle de producto
- Conexion.php # Conexión a la base de datos
- login.php, Login_validar.php, logout.php, sesion.php # Autenticación
- carrito.php, Carrito_agregar.php, Carrito_listar.php,
- Carrito_actualizar.php, Carriyo_eliminar.php # Carrito de compras
- style.css, extra.css # Estilos
- tienda.js, carrito_vista.js # Lógica en el cliente
- tienda_virtual.sql # Esquema principal de la base de datos
- 02_auth_y_carrito.sql # Esquema de autenticación y carrito

## Cómo ejecutarlo localmente

1. Instala [XAMPP](https://www.apachefriends.org/) y clona este repositorio dentro de la carpeta `htdocs`.
2. Crea una base de datos en phpMyAdmin e importa `tienda_virtual.sql` y luego `02_auth_y_carrito.sql`.
3. Ajusta las credenciales de conexión en `Conexion.php` si es necesario.
4. Inicia Apache y MySQL desde el panel de XAMPP.
5. Abre `http://localhost/tienda-virtual/index.php` en tu navegador.

## Autor

**Jorge Miguel Caso Daza**
[LinkedIn](https://www.linkedin.com/in/jorge-miguel-caso-daza-4451841b9/)
