<?php
$host = "localhost";
$usuario = "root"; 
$contrasena = "";
$basededatos = "sistema_barberia";

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

//Verificar conexion
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
?>