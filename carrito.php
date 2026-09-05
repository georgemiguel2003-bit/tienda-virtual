<?php
/**
 * VISTA DEL CARRITO (diseno libre)
 * Protegida por sesion. Los datos se cargan via SERVICIO WEB
 * (ver carrito_vista.js).
 */
require_once 'sesion.php';
$idUsuario = (int)$usuarioActual['id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Mi carrito — Tienda Virtual</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>
<header>
  <div class="header-inner">
    <h1>🛒 Tienda Virtual</h1>
    <nav>
      <a href="index.php">← Seguir comprando</a>
      <a href="logout.php">Cerrar sesión</a>
    </nav>
  </div>
</header>

<main>
  <section class="busqueda-section">
    <h2>Mi carrito de compras</h2>
    <div id="lista-carrito">Cargando...</div>
  </section>
</main>

<footer>
  <p>&copy; 2026 Tienda Virtual — Programación Web Básica</p>
</footer>

<script>
  window.ID_USUARIO = <?= $idUsuario ?>;
</script>
<script src="carrito_vista.js"></script>
</body>
</html>
