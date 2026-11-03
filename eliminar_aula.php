<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

require_once 'conexion.php';

$grado   = isset($_POST['grado']) ? intval($_POST['grado']) : 0;
$seccion = isset($_POST['seccion']) ? trim($_POST['seccion']) : '';

if ($grado <= 0 || empty($seccion)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Grado o sección inválidos.']);
    exit;
}

$conexion->begin_transaction();

try {
    // Borrar los votos de las alumnas de esta aula específica
    $stmtVotos = $conexion->prepare("DELETE v FROM votos v INNER JOIN alumnas a ON v.alumna_id = a.id WHERE a.grado = ? AND a.seccion = ?");
    $stmtVotos->bind_param("is", $grado, $seccion);
    $stmtVotos->execute();
    $stmtVotos->close();

    // Borrar a las alumnas de esa aula
    $stmtAlumnas = $conexion->prepare("DELETE FROM alumnas WHERE grado = ? AND seccion = ?");
    $stmtAlumnas->bind_param("is", $grado, $seccion);
    $stmtAlumnas->execute();
    $stmtAlumnas->close();

    $conexion->commit();
    echo json_encode(['exito' => true, 'mensaje' => "El padrón del {$grado}° \"{$seccion}\" y sus votos fueron eliminados correctamente."]);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al eliminar el aula: ' . $e->getMessage()]);
}

$conexion->close();
?>
