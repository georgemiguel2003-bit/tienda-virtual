<?php
/**
 * SERVICIO 2 — Actualizar cantidad de producto en carrito_detalle
 * ──────────────────────────────────────────────────────────────
 * Parámetros POST:
 *   detalle_id (int) → id del registro en carrito_detalle
 *   cantidad   (int) → nueva cantidad (mínimo 1)
 */

header('Content-Type: application/json');
require_once 'conexion.php';

// Recepción de parámetros POST
$detalle_id = isset($_POST['detalle_id']) ? (int)$_POST['detalle_id'] : 0;
$cantidad   = isset($_POST['cantidad'])   ? (int)$_POST['cantidad']   : 0;

// Validación
if ($detalle_id <= 0 || $cantidad < 1) {
    echo json_encode([
        'exito'   => false,
        'mensaje' => 'Parámetros inválidos. La cantidad debe ser mayor a 0.'
    ]);
    exit;
}

$pdo = Conexion::obtener();

// Verificar que el registro exista
$check = $pdo->prepare("SELECT id FROM carrito_detalle WHERE id = :id LIMIT 1");
$check->execute([':id' => $detalle_id]);

if (!$check->fetch()) {
    echo json_encode(['exito' => false, 'mensaje' => 'Registro no encontrado en el carrito.']);
    exit;
}

// Actualizar cantidad
$stmt = $pdo->prepare("UPDATE carrito_detalle SET cantidad = :cantidad WHERE id = :id");
$stmt->execute([':cantidad' => $cantidad, ':id' => $detalle_id]);

echo json_encode([
    'exito'      => true,
    'mensaje'    => "Cantidad actualizada correctamente a {$cantidad} unidad(es).",
    'detalle_id' => $detalle_id,
    'cantidad'   => $cantidad
]);