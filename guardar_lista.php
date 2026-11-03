<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    echo json_encode(['exito' => false, 'mensaje' => 'No autorizado']);
    exit;
}

require_once '../config/conexion.php';

$nombre_lista = trim($_POST['nombre_lista'] ?? '');
$lider        = trim($_POST['lider'] ?? '');
$logo_url     = 'assets/img/default-logo.png'; // Logo por defecto si no suben foto

if (empty($nombre_lista) || empty($lider)) {
    echo json_encode(['exito' => false, 'mensaje' => 'Por favor ingrese el nombre de la lista y la representante.']);
    exit;
}

// Procesar la imagen si se seleccionó una
if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['logo']['tmp_name'];
    $fileName = $_FILES['logo']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    if (in_array($fileExtension, $allowedExtensions)) {
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $uploadFileDir = '../assets/img/';
        
        if (!is_dir($uploadFileDir)) {
            mkdir($uploadFileDir, 0755, true);
        }
        
        $dest_path = $uploadFileDir . $newFileName;
        
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $logo_url = 'assets/img/' . $newFileName;
        }
    }
}

$stmt = $conexion->prepare("INSERT INTO listas (nombre_lista, lider, logo_url) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $nombre_lista, $lider, $logo_url);

if ($stmt->execute()) {
    echo json_encode(['exito' => true, 'mensaje' => 'Lista y foto registradas correctamente.']);
} else {
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos.']);
}

$stmt->close();
$conexion->close();
?>