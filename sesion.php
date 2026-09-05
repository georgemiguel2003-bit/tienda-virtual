<?php
/**
 * GUARDIAN DE SESION
 * Incluir al inicio de paginas que solo deben ver los logueados:
 *     require_once 'sesion.php';
 * Si no hay sesion, redirige al login.
 */
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuarioActual = $_SESSION['usuario'];
