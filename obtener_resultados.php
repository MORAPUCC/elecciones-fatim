<?php
header('Content-Type: application/json');
require_once 'conexion.php';

$queryVotos = "
    SELECT l.id, l.nombre_lista, COUNT(v.id) as total_votos
    FROM listas l
    LEFT JOIN votos v ON l.id = v.lista_id
    GROUP BY l.id, l.nombre_lista
";
$resVotos = $conexion->query($queryVotos);
$conteoListas = [];
while ($row = $resVotos->fetch_assoc()) {
    $conteoListas[] = $row;
}

$totalAlumnas = $conexion->query("SELECT COUNT(*) as total FROM alumnas")->fetch_assoc()['total'];
$totalVotaron = $conexion->query("SELECT COUNT(*) as total FROM alumnas WHERE ya_voto = 1")->fetch_assoc()['total'];

$resAlumnas = $conexion->query("SELECT doc_identidad, tipo_doc, nombres_apellidos, grado, seccion, ya_voto FROM alumnas ORDER BY grado ASC, seccion ASC, nombres_apellidos ASC");
$padron = [];
while ($row = $resAlumnas->fetch_assoc()) {
    $padron[] = $row;
}

echo json_encode([
    'exito' => true,
    'total_alumnas' => (int)$totalAlumnas,
    'total_votaron' => (int)$totalVotaron,
    'pendientes' => (int)($totalAlumnas - $totalVotaron),
    'resultados' => $conteoListas,
    'padron' => $padron
]);

$conexion->close();
?>
