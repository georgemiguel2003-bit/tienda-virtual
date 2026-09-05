<?php
session_start();
$logueado  = isset($_SESSION['usuario']);
$idUsuario = $logueado ? (int)$_SESSION['usuario']['id'] : 0;

$host = 'localhost';
$dbname = 'tienda_virtual';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Validar ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
$stmt->execute([':id' => $id]);
$producto = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$producto) {
    header("Location: index.php");
    exit;
}

// Productos relacionados (misma categoría, excluyendo el actual)
$rel = $pdo->prepare("SELECT * FROM productos WHERE categoria = :cat AND id != :id LIMIT 4");
$rel->execute([':cat' => $producto['categoria'], ':id' => $id]);
$relacionados = $rel->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($producto['nombre']) ?> — Tienda Virtual</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>
<header>
  <div class="header-inner">
    <h1>🛒 Tienda Virtual</h1>
    <nav>
      <a href="index.php">← Volver al catálogo</a>
      <?php if ($logueado): ?>
        <a href="carrito.php">Carrito</a>
        <a href="logout.php">Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php">LogIn</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
  <!-- VISTA DETALLE -->
  <section class="detalle-section">
    <div class="detalle-container">

      <div class="detalle-imagen">
        <img src="<?= htmlspecialchars($producto['imagen'] ?? 'img/default.jpg') ?>"
             alt="<?= htmlspecialchars($producto['nombre']) ?>">
      </div>

      <div class="detalle-info">
        <span class="badge badge-lg"><?= htmlspecialchars($producto['categoria']) ?></span>
        <h2><?= htmlspecialchars($producto['nombre']) ?></h2>

        <div class="detalle-precio">
          <span>S/. <?= number_format($producto['precio'], 2) ?></span>
        </div>

        <div class="detalle-descripcion">
          <h4>Descripción</h4>
          <p><?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción disponible.')) ?></p>
        </div>

        <div class="detalle-meta">
          <div class="meta-item">
            <span class="meta-label">Categoría</span>
            <span class="meta-valor"><?= htmlspecialchars($producto['categoria']) ?></span>
          </div>
          <div class="meta-item">
            <span class="meta-label">Stock disponible</span>
            <span class="meta-valor <?= $producto['stock'] > 0 ? 'en-stock' : 'sin-stock' ?>">
              <?= $producto['stock'] > 0 ? $producto['stock'] . ' unidades' : 'Agotado' ?>
            </span>
          </div>
          <?php if (!empty($producto['marca'])): ?>
          <div class="meta-item">
            <span class="meta-label">Marca</span>
            <span class="meta-valor"><?= htmlspecialchars($producto['marca']) ?></span>
          </div>
          <?php endif; ?>
        </div>

        <div class="detalle-acciones">
          <?php if ($producto['stock'] > 0): ?>
            <button class="btn-agregar" onclick="agregarCarrito(<?= $producto['id'] ?>)">
              🛒 Agregar al carrito
            </button>
          <?php else: ?>
            <button class="btn-agregar disabled" disabled>Producto agotado</button>
          <?php endif; ?>
          <a href="index.php" class="btn-volver">← Seguir comprando</a>
        </div>
      </div>
    </div>
  </section>

  <!-- PRODUCTOS RELACIONADOS -->
  <?php if (!empty($relacionados)): ?>
  <section class="relacionados-section">
    <h3>Productos relacionados</h3>
    <div class="productos-grid">
      <?php foreach ($relacionados as $r): ?>
        <a class="card" href="detalle.php?id=<?= $r['id'] ?>">
          <img src="<?= htmlspecialchars($r['imagen'] ?? 'img/default.jpg') ?>"
               alt="<?= htmlspecialchars($r['nombre']) ?>">
          <div class="card-body">
            <span class="badge"><?= htmlspecialchars($r['categoria']) ?></span>
            <h3><?= htmlspecialchars($r['nombre']) ?></h3>
            <p class="precio">S/. <?= number_format($r['precio'], 2) ?></p>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</main>

<footer>
  <p>&copy; 2026 Tienda Virtual — Programación Web Básica</p>
</footer>

<script>
  window.ID_USUARIO = <?= $idUsuario ?>; // 0 si no hay sesión

  // Agrega al carrito CONSUMIENDO el servicio web Carrito_agregar.php
  async function agregarCarrito(productoId) {
    if (!window.ID_USUARIO || window.ID_USUARIO <= 0) {
      if (confirm('Debes iniciar sesión para agregar productos. ¿Ir al login?')) {
        window.location.href = 'login.php';
      }
      return;
    }
    const datos = new FormData();
    datos.append('id_usuario',  window.ID_USUARIO);
    datos.append('producto_id', productoId);

    try {
      const res  = await fetch('Carrito_agregar.php', { method: 'POST', body: datos });
      const data = await res.json();
      alert(data.mensaje);
    } catch (e) {
      alert('Error de conexión con el servicio web.');
    }
  }
</script>
</body>
</html>
