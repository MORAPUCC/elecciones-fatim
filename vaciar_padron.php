<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

require_once 'conexion.php';

$conexion->begin_transaction();

try {
    // 1. Borrar todos los votos emitidos
    $conexion->query("DELETE FROM votos");
    
    // 2. Borrar todas las alumnas de los Excel cargados
    $conexion->query("DELETE FROM alumnas");
    
    // 3. Reiniciar el autoincremento de la tabla
    $conexion->query("ALTER TABLE alumnas AUTO_INCREMENT = 1");

    $conexion->commit();
    echo json_encode(['exito' => true, 'mensaje' => 'Padrón y votos borrados con éxito. Ya puedes subir nuevos Excel.']);
} catch (Exception $e) {
    $conexion->rollback();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al vaciar el padrón: ' . $e->getMessage()]);
}

$conexion->close();
?>
