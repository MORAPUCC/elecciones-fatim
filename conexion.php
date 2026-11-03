<?php
$host = "mysql-35790ab1-ucvvirtual-3217.j.aivencloud.com";
$user = "avnadmin";
$password = "AVNS_wOuXRjIds5mTQX7OAQI";
$dbname = "defaultdb";
$port = 26748;

$conexion = mysqli_init();
$conexion->ssl_set(NULL, NULL, NULL, NULL, NULL);
$conexion->real_connect($host, $user, $password, $dbname, $port, NULL, MYSQLI_CLIENT_SSL);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8mb4");
?>
