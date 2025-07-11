<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

include("conexion.php");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener usuarios
    $resultado = $conn->query("SELECT * FROM usuarios");

    $usuarios = [];
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = $fila;
    }

    echo json_encode($usuarios);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!$input) {
        echo json_encode(["success" => false, "message" => "Datos JSON no válidos"]);
        exit();
    }

    $nombre = $input['nombre'] ?? '';
    $correo = $input['correo'] ?? '';
    $genero = $input['genero'] ?? '';
    $tipo_documento = $input['tipo_documento'] ?? '';
    $numero_documento = $input['numero_documento'] ?? '';
    $rol = $input['rol'] ?? null;

    if (!$nombre || !$correo || !$rol) {
        echo json_encode(["success" => false, "message" => "Faltan campos requeridos"]);
        exit();
    }

    $sql = "INSERT INTO usuarios (nombre_completo, nombre_usuario, genero, tipo_documento, numero_documento, id_rol) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nombre, $correo, $genero, $tipo_documento, $numero_documento, $rol);

    if ($stmt->execute()) {
    $id_nuevo = $stmt->insert_id;

    $usuario = [
        "id" => $id_nuevo,
        "nombre" => $nombre,
        "correo" => $correo,
        "genero" => $genero,
        "tipo_documento" => $tipo_documento,
        "numero_documento" => $numero_documento,
        "rol" => $rol
    ];

    echo json_encode(["success" => true, "usuario" => $usuario]);
} else {
    echo json_encode(["success" => false, "message" => "Error al insertar en la base de datos"]);
}


    exit();
}
