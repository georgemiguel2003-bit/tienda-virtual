<?php
/**
 * SERVICIO 3 — Eliminar producto de carrito_detalle
 * ──────────────────────────────────────────────────
 * Parámetros POST:
 *   detalle_id (int) → id del registro en carrito_detalle a eliminar
 */

header('Content-Type: application/json');
require_once 'conexion.php';

// Recepción de parámetros POST
$detalle_id = isset($_POST['detalle_id']) ? (int)$_POST['detalle_id'] : 0;

// Validación
if ($detalle_id <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Parámetro inválido.']);
    exit;
}

$pdo = Conexion::obtener();

// Verificar que el registro exista antes de eliminar
$check = $pdo->prepare("SELECT id FROM carrito_detalle WHERE id = :id LIMIT 1");
$check->execute([':id' => $detalle_id]);

if (!$check->fetch()) {
    echo json_encode(['exito' => false, 'mensaje' => 'El producto no existe en el carrito.']);
    exit;
}

// Eliminar el producto del detalle
$stmt = $pdo->prepare("DELETE FROM carrito_detalle WHERE id = :id");
$stmt->execute([':id' => $detalle_id]);

echo json_encode([
    'exito'      => true,
    'mensaje'    => '🗑️ Producto eliminado del carrito correctamente.',
    'detalle_id' => $detalle_id
]);