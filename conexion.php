<?php
$host = "mysql-35790ab1-ucvvirtual-3217.j.aivencloud.com";
$user = "avnadmin";
$password = "AVNS_wOuXRjIds5mTQX7OAQI";
$dbname = "defaultdb";
$port = 26748;

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conexion = mysqli_init();
    $conexion->ssl_set(NULL, NULL, NULL, NULL, NULL);
    $conexion->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);
    $conexion->set_charset("utf8mb4");
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['exito' => false, 'mensaje' => 'Error de BD: ' . $e->getMessage()]);
    exit;
}
?>
