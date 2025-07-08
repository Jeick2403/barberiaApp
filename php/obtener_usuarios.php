<?php
include("conexion.php");

// Consulta SQL
$sql = "SELECT id_usuario, nombre_completo, nombre_usuario, id_rol, genero, tipo_documento, numero_documento FROM usuarios";
$resultado = $conn->query($sql);

$usuarios = [];

if ($resultado && $resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {
        $usuarios[] = [
            'id' => $fila['id_usuario'],
            'nombre' => $fila['nombre_completo'],
            'correo' => $fila['nombre_usuario'],
            'rol' => $fila['id_rol'],
            'genero' => $fila['genero'],
            'tipo_documento' => $fila['tipo_documento'],
            'numero_documento' => $fila['numero_documento']
        ];
    }
}

// Respuesta JSON al frontend
echo json_encode($usuarios);
