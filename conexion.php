<?php
$host = "mysql-35790ab1-ucvvirtual-3217.j.aivencloud.com";
$user = "avnadmin";
$pass = "AVNS_wOuXRjIds5mTQX7OAQI";
$db   = "defaultdb";

$conexion = new mysqli($host,$user, $pass,$db);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]));
}

$conexion->set_charset("utf8mb4");
?>
