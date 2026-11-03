<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = trim($_POST['clave'] ?? '');

    if (empty($usuario) || empty($clave)) {
        header("Location: ../login.php?error=empty");
        exit;
    }

    // DEFINIMOS LOS USUARIOS Y CLAVES FIJOS DIRECTAMENTE AQUÍ
    // cables de los admin usario y la contrseña
    $usuarios_validos = [
        'rivers' => '02621681',  // Contraseña fija para rivers
        'admin'  => 'mramosch01'   // Contraseña fija para admin
    ];

    // parte de validacion 
    if (array_key_exists($usuario, $usuarios_validos) && $usuarios_validos[$usuario] === $clave) {
        $_SESSION['admin_logged'] = true;
        $_SESSION['admin_nombre'] = ($usuario === 'rivers') ? 'Profesor Rivers' : 'Administrador Principal';
        $_SESSION['admin_usuario'] = $usuario;
        
        header("Location: ../admin.php");
        exit;
    }

    // Si falla, regresa con error
    header("Location: ../login.php?error=invalid");
    exit;
}
?>