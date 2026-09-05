<?php
session_start();
$logueado  = isset($_SESSION['usuario']);
$idUsuario = $logueado ? (int)$_SESSION['usuario']['id'] : 0;

// Conexión a la base de datos
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

// Búsqueda avanzada
$nombre     = isset($_GET['nombre'])    ? trim($_GET['nombre'])    : '';
$categoria  = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
$precio_min = isset($_GET['precio_min']) && $_GET['precio_min'] !== '' ? (float)$_GET['precio_min'] : null;
$precio_max = isset($_GET['precio_max']) && $_GET['precio_max'] !== '' ? (float)$_GET['precio_max'] : null;
$orden      = isset($_GET['orden'])     ? $_GET['orden']           : 'nombre_asc';

// ── PAGINADO ──────────────────────────────────────
$por_pagina   = 4; // productos por página
$pagina_actual = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;

// WHERE dinámico (compartido para COUNT y SELECT)
$where  = "WHERE 1=1";
$params = [];

if ($nombre !== '') {
    $where .= " AND nombre LIKE :nombre";
    $params[':nombre'] = "%$nombre%";
}
if ($categoria !== '') {
    $where .= " AND categoria = :categoria";
    $params[':categoria'] = $categoria;
}
if ($precio_min !== null) {
    $where .= " AND precio >= :precio_min";
    $params[':precio_min'] = $precio_min;
}
if ($precio_max !== null) {
    $where .= " AND precio <= :precio_max";
    $params[':precio_max'] = $precio_max;
}

// ORDER BY
switch ($orden) {
    case 'precio_asc':  $order = "ORDER BY precio ASC";   break;
    case 'precio_desc': $order = "ORDER BY precio DESC";  break;
    case 'nombre_desc': $order = "ORDER BY nombre DESC";  break;
    default:            $order = "ORDER BY nombre ASC";   break;
}

// Total de resultados (para calcular páginas)
$stmt_total = $pdo->prepare("SELECT COUNT(*) FROM productos $where");
$stmt_total->execute($params);
$total_productos = (int)$stmt_total->fetchColumn();
$total_paginas   = max(1, ceil($total_productos / $por_pagina));

// Ajustar página actual si excede el total
if ($pagina_actual > $total_paginas) $pagina_actual = $total_paginas;
$offset = ($pagina_actual - 1) * $por_pagina;

// Productos de la página actual
$stmt = $pdo->prepare("SELECT * FROM productos $where $order LIMIT :limit OFFSET :offset");
foreach ($params as $k => $v) $stmt->bindValue($k, $v);
$stmt->bindValue(':limit',  $por_pagina,   PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset,       PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Categorías para el filtro
$cats = $pdo->query("SELECT DISTINCT categoria FROM productos ORDER BY categoria")->fetchAll(PDO::FETCH_COLUMN);

// Helper: construye query string conservando los filtros activos
function qstr(array $extra = []): string {
    $base = $_GET;
    unset($base['pagina']); // al cambiar filtro siempre vuelve a pág 1
    return http_build_query(array_merge($base, $extra));
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tienda Virtual</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>
<header>
  <div class="header-inner">
    <h1>🛒 Tienda Virtual</h1>
    <nav>
      <a href="index.php">Inicio</a>
      <?php if ($logueado): ?>
        <a href="carrito.php">Carrito</a>
        <span class="nav-usuario">Hola, <?= htmlspecialchars($_SESSION['usuario']['nombre']) ?></span>
        <a href="logout.php">Cerrar sesión</a>
      <?php else: ?>
        <a href="login.php">LogIn</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main>
  <!-- BÚSQUEDA AVANZADA -->
  <section class="busqueda-section">
    <h2>Búsqueda Avanzada</h2>
    <form method="GET" action="index.php" class="form-busqueda">
      <div class="form-grid">
        <div class="form-group">
          <label for="nombre">Nombre del producto</label>
          <input type="text" id="nombre" name="nombre" placeholder="Ej: Laptop..."
                 value="<?= htmlspecialchars($nombre) ?>">
        </div>
        <div class="form-group">
          <label for="categoria">Categoría</label>
          <select id="categoria" name="categoria">
            <option value="">-- Todas --</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= htmlspecialchars($c) ?>" <?= $categoria === $c ? 'selected' : '' ?>>
                <?= htmlspecialchars($c) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label for="precio_min">Precio mínimo (S/.)</label>
          <input type="number" id="precio_min" name="precio_min" min="0" step="0.01"
                 value="<?= $precio_min !== null ? $precio_min : '' ?>">
        </div>
        <div class="form-group">
          <label for="precio_max">Precio máximo (S/.)</label>
          <input type="number" id="precio_max" name="precio_max" min="0" step="0.01"
                 value="<?= $precio_max !== null ? $precio_max : '' ?>">
        </div>
        <div class="form-group">
          <label for="orden">Ordenar por</label>
          <select id="orden" name="orden">
            <option value="nombre_asc"  <?= $orden==='nombre_asc'  ? 'selected':'' ?>>Nombre A-Z</option>
            <option value="nombre_desc" <?= $orden==='nombre_desc' ? 'selected':'' ?>>Nombre Z-A</option>
            <option value="precio_asc"  <?= $orden==='precio_asc'  ? 'selected':'' ?>>Precio menor a mayor</option>
            <option value="precio_desc" <?= $orden==='precio_desc' ? 'selected':'' ?>>Precio mayor a menor</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn-buscar">🔍 Buscar</button>
        <a href="index.php" class="btn-limpiar">✖ Limpiar</a>
      </div>
    </form>
  </section>

  <!-- RESULTADOS -->
  <section class="productos-section">
    <h2>
      Productos
      <span class="contador">
        (<?= $total_productos ?> resultado<?= $total_productos !== 1 ? 's' : '' ?>)
      </span>
    </h2>

    <?php if (empty($productos)): ?>
      <div class="sin-resultados">
        <p>😕 No se encontraron productos con esos criterios.</p>
        <a href="index.php" class="btn-limpiar">Ver todos los productos</a>
      </div>
    <?php else: ?>
      <div class="productos-grid">
        <?php foreach ($productos as $p): ?>
          <!-- La tarjeta abre el MODAL de detalle (vista detalle en modal) -->
          <div class="card"
               data-id="<?= $p['id'] ?>"
               data-nombre="<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>"
               data-precio="<?= $p['precio'] ?>"
               data-imagen="<?= htmlspecialchars($p['imagen'] ?? 'img/default.jpg', ENT_QUOTES) ?>"
               data-categoria="<?= htmlspecialchars($p['categoria'], ENT_QUOTES) ?>"
               data-marca="<?= htmlspecialchars($p['marca'] ?? '', ENT_QUOTES) ?>"
               data-stock="<?= $p['stock'] ?>"
               data-descripcion="<?= htmlspecialchars($p['descripcion'] ?? '', ENT_QUOTES) ?>"
               onclick="abrirModal(this)" style="cursor:pointer;">
            <img src="<?= htmlspecialchars($p['imagen'] ?? 'img/default.jpg') ?>"
                 alt="<?= htmlspecialchars($p['nombre']) ?>">
            <div class="card-body">
              <span class="badge"><?= htmlspecialchars($p['categoria']) ?></span>
              <h3><?= htmlspecialchars($p['nombre']) ?></h3>
              <p class="precio">S/. <?= number_format($p['precio'], 2) ?></p>
              <span class="stock <?= $p['stock'] > 0 ? 'en-stock' : 'sin-stock' ?>">
                <?= $p['stock'] > 0 ? "En stock ({$p['stock']})" : "Agotado" ?>
              </span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <!-- ── COMPONENTE DE PAGINADO ───────────────────────────── -->
      <?php if ($total_paginas > 1): ?>
      <nav class="paginado" aria-label="Paginación de productos">

        <?php if ($pagina_actual > 1): ?>
          <a class="pag-btn pag-prev" href="?<?= qstr(['pagina' => $pagina_actual - 1]) ?>">
            &#8592; Previous
          </a>
        <?php else: ?>
          <span class="pag-btn pag-prev disabled">&#8592; Previous</span>
        <?php endif; ?>

        <div class="pag-numeros" id="paginadoBotones">
          <?php
          $ventana = 2;
          $inicio  = max(1, $pagina_actual - $ventana);
          $fin     = min($total_paginas, $pagina_actual + $ventana);

          if ($inicio > 1): ?>
            <a class="pag-btn" href="?<?= qstr(['pagina' => 1]) ?>">1</a>
            <?php if ($inicio > 2): ?>
              <span class="pag-ellipsis">…</span>
            <?php endif; ?>
          <?php endif; ?>

          <?php for ($i = $inicio; $i <= $fin; $i++): ?>
            <?php if ($i === $pagina_actual): ?>
              <span class="pag-btn activo" aria-current="page"><?= $i ?></span>
            <?php else: ?>
              <a class="pag-btn" href="?<?= qstr(['pagina' => $i]) ?>"><?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($fin < $total_paginas): ?>
            <?php if ($fin < $total_paginas - 1): ?>
              <span class="pag-ellipsis">…</span>
            <?php endif; ?>
            <a class="pag-btn" href="?<?= qstr(['pagina' => $total_paginas]) ?>"><?= $total_paginas ?></a>
          <?php endif; ?>
        </div>

        <?php if ($pagina_actual < $total_paginas): ?>
          <a class="pag-btn pag-next" href="?<?= qstr(['pagina' => $pagina_actual + 1]) ?>">
            Next &#8594;
          </a>
        <?php else: ?>
          <span class="pag-btn pag-next disabled">Next &#8594;</span>
        <?php endif; ?>

      </nav>
      <p class="pag-info">Página <?= $pagina_actual ?> de <?= $total_paginas ?></p>
      <?php endif; ?>

    <?php endif; ?>
  </section>
</main>

<footer>
  <p>&copy; 2026 Tienda Virtual — Programación Web Básica</p>
</footer>

<!-- ============ MODAL DE DETALLE DE PRODUCTO ============ -->
<div id="modal-producto" class="overlay-modal">
  <div class="modal-caja">
    <button class="modal-cerrar" onclick="cerrarModal()">&times;</button>
    <div class="modal-grid">
      <div class="modal-img-wrap">
        <img id="m-imagen" src="" alt="">
      </div>
      <div class="modal-info">
        <span class="badge badge-lg" id="m-categoria"></span>
        <h2 id="m-nombre"></h2>
        <div class="detalle-precio"><span id="m-precio"></span></div>
        <p id="m-desc" class="modal-desc"></p>
        <div class="detalle-meta">
          <div class="meta-item"><span class="meta-label">Marca</span><span class="meta-valor" id="m-marca"></span></div>
          <div class="meta-item"><span class="meta-label">Stock</span><span class="meta-valor" id="m-stock"></span></div>
        </div>
        <input type="hidden" id="m-id">
        <button class="btn-agregar" id="m-btn-agregar" onclick="agregarAlCarrito()">🛒 Agregar al carrito</button>
      </div>
    </div>
  </div>
</div>

<script>
  window.ID_USUARIO = <?= $idUsuario ?>; // 0 si no hay sesión
</script>
<script src="tienda.js"></script>
</body>
</html>
