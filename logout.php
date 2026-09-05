<?php
/**
 * LOGOUT — cierra sesion y elimina los datos de sesion
 */
session_start();
$_SESSION = [];        // vacia las variables de sesion
session_destroy();     // destruye la sesion
header('Location: index.php');
exit;
