<?php
header('Content-Type: application/json');
require_once '../config/conexion.php';

// Si recibes los datos mediante JSON desde el JS del admin o formulario:
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['alumnas']) || !is_array($data['alumnas'])) {
    echo json_encode(['exito' => false, 'mensaje' => 'No se recibieron datos de alumnas válidos.']);
    exit;
}

$alumnas = $data['alumnas'];
$insertadas = 0;
$errores = 0;

$stmt = $conexion->prepare("
    INSERT INTO alumnas (doc_identidad, tipo_doc, nombres_apellidos, grado, seccion) 
    VALUES (?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE 
    tipo_doc = VALUES(tipo_doc),
    nombres_apellidos = VALUES(nombres_apellidos),
    grado = VALUES(grado),
    seccion = VALUES(seccion)
");

foreach ($alumnas as $row) {
    $documento = trim($row['documento'] ?? '');
    $tipo = trim($row['tipo_doc'] ?? 'DNI');
    $nombre = trim($row['nombres_apellidos'] ?? '');
    $grado = intval($row['grado'] ?? 0);
    $seccion = strtoupper(trim($row['seccion'] ?? ''));

    // Filtro de seguridad: validamos que el documento tenga pinta de DNI o número y no sea un título de cabecera
    if (!empty($documento) && !empty($nombre) && $grado > 0 && !empty($seccion)) {
        // Evitamos que guarde textos de cabecera si el Excel los coló por error
        if (is_numeric($documento) || strlen($documento) >= 6) {
            $stmt->bind_param("sssis", $documento, $tipo, $nombre, $grado, $seccion);
            if ($stmt->execute()) {
                $insertadas++;
            } else {
                $errores++;
            }
        }
    }
}

$stmt->close();
$conexion->close();

echo json_encode([
    'exito' => true,
    'mensaje' => "Se registraron / actualizaron correctamente $insertadas alumnas en el padrón.",
    'insertadas' => $insertadas,
    'errores' => $errores
]);
?>