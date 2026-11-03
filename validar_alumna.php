<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$grado = $_POST['grado'] ?? '';
$seccion = $_POST['seccion'] ?? '';
$documento = trim($_POST['documento'] ?? '');

if (empty($grado) || empty($seccion) || empty($documento)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Faltan datos obligatorios.']);
    exit;
}

// Buscamos a la alumna en la base de datos
$stmt = $conexion->prepare("SELECT id, nombres_apellidos, ya_voto FROM alumnas WHERE grado = ? AND seccion = ? AND doc_identidad = ?");
$stmt->bind_param("iss", $grado, $seccion, $documento);
$stmt->execute();
$resultado = $stmt->get_result();

if ($row = $resultado->fetch_assoc()) {
    if ($row['ya_voto'] == 1) {
        echo json_encode(['exito' => false, 'mensaje' => 'Esta alumna ya ha emitido su voto anteriormente.']);
    } else {
        // Devolvemos el nombre con todas las claves posibles para evitar el 'undefined'
        $nombreCompleto = $row['nombres_apellidos'];
        echo json_encode([
            'exito' => true,
            'id' => $row['id'],
            'nombre' => $nombreCompleto,
            'nombres' => $nombreCompleto,
            'nombres_apellidos' => $nombreCompleto,
            'mensaje' => 'Alumna válida'
        ]);
    }
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'No se encontró a la alumna con ese documento en el aula seleccionada. Verifique los datos o el padrón.']);
}

$stmt->close();
$conexion->close();
?>
