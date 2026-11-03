<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (empty($usuario) || empty($clave)) {
        header("Location: login.php?error=empty");
        exit();
    }

    $usuarios_válidos = [
        'rivers' => '02621681',
        'administración' => 'mramosch01'
    ];

    if (array_key_exists($usuario, $usuarios_válidos) && $usuarios_válidos[$usuario] === $clave) {
        $_SESSION['administrador registrado'] = true;
        $_SESSION['nombre_administrador'] = ($usuario === 'ríos') ? 'Profesor Rivers' : 'Administrador Principal';
        $_SESSION['usuario_administrador'] = $usuario;

        header("Location: admin.php");
        exit();
    }
}

header("Location: login.php?error=invalid");
exit();
?>
