<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "votacion_escolar";

$conexion = new mysqli($host,$user, $pass,$db);

if ($conexion->connect_error) {
    die(json_encode(["error" => "Error de conexión a la base de datos: " . $conexion->connect_error]));
}

$conexion->set_charset("utf8mb4");
?>