<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

require_once '../config/conexion.php';

$id_lista = isset($_POST['id']) ? intval($_POST['id']) : 0;

if ($id_lista <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'ID de lista inválido']);
    exit;
}

// Opcional: Primero eliminamos los votos asociados a esta lista para evitar conflictos
$stmtVotos = $conexion->prepare("DELETE FROM votos WHERE lista_id = ?");
$stmtVotos->bind_param("i", $id_lista);
$stmtVotos->execute();
$stmtVotos->close();

// Luego eliminamos la lista
$stmt = $conexion->prepare("DELETE FROM listas WHERE id = ?");
$stmt->bind_param("i", $id_lista);

if ($stmt->execute()) {
    echo json_encode(['exito' => true, 'mensaje' => 'Lista eliminada correctamente.']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar la lista de la base de datos.']);
}

$stmt->close();
$conexion->close();
?>