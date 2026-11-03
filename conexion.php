<?php
$host = "mysql-35790ab1-ucvvirtual-3217.j.aivencloud.com";
$user = "avnadmin";
$password = "AVNS_wOuXRjIds5mTQX7OAQI";
$dbname = "defaultdb";
$port = 26748;

$conexion = mysqli_init();

// Desactiva la verificación estricta del certificado SSL para permitir la conexión desde Render
mysqli_options($conexion, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

// Conexión segura con Aiven
if (!@$conexion->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Error de conexión: ' . mysqli_connect_error()]);
    exit;
}

$conexion->set_charset("utf8mb4");
?>
