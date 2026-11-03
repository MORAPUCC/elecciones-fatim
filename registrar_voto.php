<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$alumna_id = isset($_POST['alumna_id']) ? intval($_POST['alumna_id']) : 0;
$lista_id  = isset($_POST['lista_id']) ? intval($_POST['lista_id']) : 0;

if ($alumna_id <= 0 || $lista_id <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Datos de voto inválidos.']);
    exit;
}

$check = $conexion->prepare("SELECT ya_voto, grado, seccion FROM alumnas WHERE id = ?");
$check->bind_param("i", $alumna_id);
$check->execute();
$resCheck = $check->get_result()->fetch_assoc();

if (!$resCheck || $resCheck['ya_voto'] == 1) {
    echo json_encode(['exito' => false, 'mensaje' => 'Su voto ya fue registrado previamente.']);
    exit;
}
$grado = $resCheck['grado'];
$seccion = $resCheck['seccion'];

$conexion->begin_transaction();

try {
    $stmt1 = $conexion->prepare("INSERT INTO votos (alumna_id, lista_id, grado, seccion) VALUES (?, ?, ?, ?)");
    $stmt1->bind_param("iiis", $alumna_id, $lista_id, $grado, $seccion);
    $stmt1->execute();

    $stmt2 = $conexion->prepare("UPDATE alumnas SET ya_voto = 1 WHERE id = ?");
    $stmt2->bind_param("i", $alumna_id);
    $stmt2->execute();

    $conexion->commit();
    echo json_encode(['exito' => true, 'mensaje' => '¡Voto registrado con éxito!']);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar el voto.']);
}

$conexion->close();
?>
