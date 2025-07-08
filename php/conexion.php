<?php
// Reporte de errores (opcional, para desarrollo)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Conexión
$host = "localhost";
$usuario = "root";
$contrasena = "";
$basededatos = "sistema_barberia";

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

// Verificar conexión
if ($conn->connect_error) {
    // Enviar error como JSON si falla
    header("Content-Type: application/json");
    echo json_encode([
        "success" => false,
        "message" => "Conexión fallida: " . $conn->connect_error
    ]);
    exit();
}
?>
