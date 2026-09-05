<?php
/**
 * SERVICIO 1 — Agregar producto al carrito
 * ─────────────────────────────────────────
 * - Si el usuario NO tiene carrito → crea registro en tabla "carrito"
 * - Si el producto YA está en carrito_detalle → suma 1 a cantidad
 * - Si el producto NO está en carrito_detalle → inserta nuevo registro
 *
 * Parámetros POST:
 *   id_usuario  (int)
 *   producto_id (int)
 */

header('Content-Type: application/json');
require_once 'conexion.php';

// Recepción de parámetros POST
$id_usuario  = isset($_POST['id_usuario'])  ? (int)$_POST['id_usuario']  : 0;
$producto_id = isset($_POST['producto_id']) ? (int)$_POST['producto_id'] : 0;

// Validación básica
if ($id_usuario <= 0 || $producto_id <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Parámetros inválidos.']);
    exit;
}

$pdo = Conexion::obtener();

// 1. Verificar si el usuario ya tiene un carrito activo
$stmt = $pdo->prepare("SELECT id FROM carrito WHERE id_usuario = :id_usuario LIMIT 1");
$stmt->execute([':id_usuario' => $id_usuario]);
$carrito = $stmt->fetch();

if ($carrito) {
    // Carrito existente
    $carrito_id = $carrito['id'];

    // Actualizar fecha de modificación
    $pdo->prepare("UPDATE carrito SET fecha_actualizacion = NOW() WHERE id = :id")
        ->execute([':id' => $carrito_id]);
} else {
    // Crear nuevo carrito para el usuario
    $ins = $pdo->prepare("INSERT INTO carrito (id_usuario, fecha_creacion, fecha_actualizacion)
                          VALUES (:id_usuario, NOW(), NOW())");
    $ins->execute([':id_usuario' => $id_usuario]);
    $carrito_id = (int)$pdo->lastInsertId();
}

// 2. Verificar si el producto ya está en carrito_detalle
$stmt2 = $pdo->prepare("SELECT id, cantidad FROM carrito_detalle
                         WHERE carrito_id = :carrito_id AND producto_id = :producto_id
                         LIMIT 1");
$stmt2->execute([':carrito_id' => $carrito_id, ':producto_id' => $producto_id]);
$detalle = $stmt2->fetch();

if ($detalle) {
    // Producto existe → aumentar cantidad
    $nueva_cantidad = $detalle['cantidad'] + 1;
    $upd = $pdo->prepare("UPDATE carrito_detalle SET cantidad = :cantidad
                           WHERE id = :id");
    $upd->execute([':cantidad' => $nueva_cantidad, ':id' => $detalle['id']]);

    echo json_encode([
        'exito'   => true,
        'mensaje' => "Cantidad actualizada. Ahora tienes {$nueva_cantidad} unidad(es) de este producto.",
        'accion'  => 'actualizado'
    ]);
} else {
    // Producto no existe → insertar en detalle
    $ins2 = $pdo->prepare("INSERT INTO carrito_detalle (carrito_id, producto_id, cantidad)
                            VALUES (:carrito_id, :producto_id, 1)");
    $ins2->execute([':carrito_id' => $carrito_id, ':producto_id' => $producto_id]);

    echo json_encode([
        'exito'   => true,
        'mensaje' => '✅ Producto agregado al carrito correctamente.',
        'accion'  => 'insertado'
    ]);
}