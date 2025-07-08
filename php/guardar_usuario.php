<?php
/*
//Reporte de errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include("conexion.php");

// Lee el JSON recibido
$data = json_decode(file_get_contents("php://input"), true);

if ($data === null) {
    echo json_encode(["success" => false, "message" => "Error: JSON no válido o vacío"]);
    exit();
}


// Verifica que se recibieron todos los campos
if (
    isset($data['nombre']) &&
    isset($data['correo']) &&
    isset($data['genero']) &&
    isset($data['tipo_documento']) &&
    isset($data['numero_documento']) &&
    isset($data['rol'])
) {
    $nombre = $data['nombre'];
    $correo = $data['correo'];
    $genero = $data['genero'];
    $tipo_documento = $data['tipo_documento'];
    $numero_documento = $data['numero_documento'];
    $rol = $data['rol'];

    $sql = "INSERT INTO usuarios (nombre_usuario, correo, genero, tipo_documento, numero_documento, id_rol) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $correo, $genero, $tipo_documento, $numero_documento, $rol);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Usuario guardado con éxito"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al guardar en la base de datos"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Faltan datos requeridos"]);
}
?>
*/