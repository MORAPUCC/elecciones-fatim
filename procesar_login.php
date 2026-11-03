<?php
session_start();

$usuario = trim($_POST['usuario'] ?? '');
$clave = trim($_POST['clave'] ?? '');

if (empty($usuario) || empty($clave)) {
    header("Location: login.php?error=empty");
    exit;
}

// Convertimos el usuario ingresado a minúsculas para evitar problemas con mayúsculas/minúsculas
$usuario_limpio = strtolower($usuario);

// Credenciales válidas permitidas
$credenciales_validas = [
    "admin" => "02621681",
    "ríos" => "02621681",
    "rios" => "02621681",
    "rivers" => "02621681",          // Añadido para que te acepte rivers
    "administracion" => "mramosch01"
];

if (array_key_exists($usuario_limpio, $credenciales_validas) && $credenciales_validas[$usuario_limpio] === $clave) {
    $_SESSION['admin_logged'] = true;
    $_SESSION['usuario_actual'] = $usuario;
    header("Location: admin.php");
    exit;
} else {
    header("Location: login.php?error=credentials");
    exit;
}
?>
