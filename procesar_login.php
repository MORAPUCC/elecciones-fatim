<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (empty($usuario) || empty($clave)) {
        header("Location: ../login.php?error=empty");
        exit();
    }
    // Definimos los usuarios y claves válidos directamente aquí
    $usuarios_válidos = [
        'rivers' => '02621681',         // Contraseña fija para ríos
        'administración' => 'mramosch01' // Contraseña fija para admin
    ];
    // Parte de validación
    if (array_key_exists($usuario, $usuarios_válidos) && $usuarios_válidos[$usuario] === $clave) {
        $_SESSION['administrador registrado'] = true;
        $_SESSION['nombre_administrador'] = ($usuario === 'ríos') ? 'Profesor Rivers' : 'Administrador Principal';
        $_SESSION['usuario_administrador'] = $usuario;

        header("Location: ../admin.php");
        exit();
    }
}
// Si falla, regresa con error
header("Location: ../login.php?error=invalid");
exit();
?>
