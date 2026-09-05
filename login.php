<?php
/**
 * PAGINA DE LOGIN
 * - Muestra el formulario.
 * - Al enviar, CONSUME el servicio web (Login_validar.php) con cURL.
 * - Si valida, GUARDA LA SESION en PHP y redirige a index.php.
 *
 * La URL del servicio se construye de forma segura aunque la
 * carpeta del proyecto tenga espacios (ej. "Mi Proyecto").
 */
session_start();

// Si ya hay sesion, va directo a la tienda
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = trim($_POST['correo'] ?? '');
    $clave  = $_POST['clave'] ?? '';

    // ---- Construir la URL del servicio (codificando espacios) ----
    $esquema = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $carpeta = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])); // ej: /Mi Proyecto
    $carpeta = rtrim($carpeta, '/');
    // Codifica cada segmento de la ruta (convierte "Mi Proyecto" -> "Mi%20Proyecto")
    $carpeta = implode('/', array_map('rawurlencode', explode('/', $carpeta)));
    $urlServicio = $esquema . '://' . $_SERVER['HTTP_HOST'] . $carpeta . '/Login_validar.php';

    // ---- CONSUMIR EL SERVICIO WEB con cURL ----
    $ch = curl_init($urlServicio);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query(['correo' => $correo, 'clave' => $clave]),
        CURLOPT_TIMEOUT        => 10,
    ]);
    $respuesta = curl_exec($ch);
    $errCurl   = curl_error($ch);
    curl_close($ch);

    $data = json_decode($respuesta, true);

    if (is_array($data) && !empty($data['exito'])) {
        // ---- GUARDAR LA SESION EN PHP ----
        $_SESSION['usuario'] = $data['usuario'];
        header('Location: index.php');
        exit;
    } else {
        // Muestra el mensaje del servicio, o el error de cURL si no respondio
        $error = $data['mensaje'] ?? ($errCurl !== '' ? 'cURL: ' . $errCurl : 'No se pudo validar el acceso.');
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Iniciar sesion - Tienda Virtual</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="extra.css">
</head>
<body>
<header>
  <div class="header-inner">
    <h1>🛒 Tienda Virtual</h1>
    <nav><a href="index.php">&larr; Volver a la tienda</a></nav>
  </div>
</header>

<main>
  <div class="login-card">
    <h2>Iniciar sesion</h2>

    <?php if ($error): ?>
      <div class="login-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
      <div class="form-group">
        <label for="correo">Correo</label>
        <input type="email" id="correo" name="correo" required>
      </div>
      <div class="form-group">
        <label for="clave">Clave</label>
        <input type="password" id="clave" name="clave" required>
      </div>
      <button type="submit" class="btn-buscar" style="width:100%; margin-top:6px;">Ingresar</button>
    </form>

    <p class="login-pista"></p>
  </div>
</main>

<footer>
  <p>&copy; 2026 Tienda Virtual - Programacion Web Basica</p>
</footer>
</body>
</html>