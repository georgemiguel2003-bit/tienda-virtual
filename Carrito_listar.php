<?php
/**
 * SERVICIO 4 — Listar el carrito del usuario
 * ───────────────────────────────────────────
 * Devuelve los productos del carrito + total.
 *
 * Parametros POST:
 *   id_usuario (int)
 */

header('Content-Type: application/json');
require_once 'Conexion.php';

$id_usuario = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;

if ($id_usuario <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Parámetros inválidos.']);
    exit;
}

$pdo = Conexion::obtener();

$sql = "SELECT cd.id            AS detalle_id,
               p.id             AS producto_id,
               p.nombre,
               p.precio,
               p.imagen,
               cd.cantidad,
               (p.precio * cd.cantidad) AS subtotal
        FROM carrito c
        INNER JOIN carrito_detalle cd ON cd.carrito_id  = c.id
        INNER JOIN productos       p  ON p.id           = cd.producto_id
        WHERE c.id_usuario = :id_usuario
        ORDER BY cd.id";

$stmt = $pdo->prepare($sql);
$stmt->execute([':id_usuario' => $id_usuario]);
$items = $stmt->fetchAll();

$total = 0;
foreach ($items as $it) {
    $total += (float)$it['subtotal'];
}

echo json_encode([
    'exito' => true,
    'items' => $items,
    'total' => $total
]);
