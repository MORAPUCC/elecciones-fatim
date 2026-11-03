<?php
session_start();

$usuario = trim($_POST['usuario'] ?? '');
$clave = trim($_POST['clave'] ?? '');

if (empty($usuario) || empty($clave)) {
    header("Location: login.php?error=empty");
    exit;
}

// Lista de usuarios y contraseñas permitidos
$credenciales_validas = [
    "ríos" => "02621681",
    "administracion" => "mramosch01" // Puedes cambiar o ajustar el segundo usuario aquí
];

// Verificamos si el usuario existe y su contraseña coincide
if (array_key_exists($usuario, $credenciales_validas) && $credenciales_validas[$usuario] === $clave) {
    $_SESSION['admin_logged'] = true;
    $_SESSION['usuario_actual'] = $usuario;
    header("Location: admin.php");
    exit;
} else {
    header("Location: login.php?error=credentials");
    exit;
}
?>
