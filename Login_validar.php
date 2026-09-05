<?php
/**
 * SERVICIO WEB — Validacion de login
 * ──────────────────────────────────
 * INVOCA el Procedimiento Almacenado sp_validar_usuario
 * y devuelve el resultado en JSON.
 *
 * Parametros POST:
 *   correo (string)
 *   clave  (string)
 */

header('Content-Type: application/json');
require_once 'Conexion.php';

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$clave  = isset($_POST['clave'])  ? $_POST['clave']        : '';

if ($correo === '' || $clave === '') {
    echo json_encode(['exito' => false, 'mensaje' => 'Debe ingresar correo y clave.']);
    exit;
}

$pdo = Conexion::obtener();

// INVOCAR EL PROCEDIMIENTO ALMACENADO
$stmt = $pdo->prepare('CALL sp_validar_usuario(?, ?)');
$stmt->execute([$correo, $clave]);
$usuario = $stmt->fetch();
$stmt->closeCursor();

if ($usuario) {
    echo json_encode([
        'exito'   => true,
        'mensaje' => 'Acceso correcto.',
        'usuario' => $usuario
    ]);
} else {
    echo json_encode([
        'exito'   => false,
        'mensaje' => 'Correo o clave incorrectos.'
    ]);
}
