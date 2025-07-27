<?php
// Configuración de cabeceras para JSON y CORS
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Incluir la conexión a la base de datos
include("conexion.php");

// Responder OPTIONS para CORS y salir
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función auxiliar para responder y terminar la ejecución
function responder($success, $message = '', $data = []) {
    echo json_encode(array_merge(
        ['success' => $success, 'message' => $message],
        $data
    ));
    exit();
}

// Habilitar excepciones en errores de MySQLi
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Determinar qué acción ejecutar según el método HTTP
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        // Obtener todos los usuarios
        $sql = "SELECT id_usuario, nombre_completo, nombre_usuario, id_rol, genero, tipo_documento, numero_documento FROM usuarios";
        $resultado = $conn->query($sql);

        $usuarios = [];
        while ($fila = $resultado->fetch_assoc()) {
            // Mapear nombres de campos para el frontend
            $usuarios[] = [
                'id'               => $fila['id_usuario'],
                'nombre'           => $fila['nombre_completo'],
                'correo'           => $fila['nombre_usuario'],
                'rol'              => $fila['id_rol'],
                'genero'           => $fila['genero'],
                'tipo_documento'   => $fila['tipo_documento'],
                'numero_documento' => $fila['numero_documento']
            ];
        }

        echo json_encode($usuarios);
        break;

    case 'POST':
        // Crear un nuevo usuario
        $input = json_decode(file_get_contents("php://input"), true);
        if (!$input) {
            responder(false, "Datos JSON no válidos");
        }

        // Campos requeridos
        $nombre          = $input['nombre']          ?? '';
        $correo          = $input['correo']          ?? '';
        $genero          = $input['genero']          ?? '';
        $tipo_documento  = $input['tipo_documento']  ?? '';
        $numero_documento= $input['numero_documento']?? '';
        $rol             = intval($input['rol']      ?? 0);

        if (!$nombre || !$correo || !$rol) {
            responder(false, "Faltan campos requeridos");
        }

        $sql = "INSERT INTO usuarios 
                (nombre_completo, nombre_usuario, genero, tipo_documento, numero_documento, id_rol)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssssi",
            $nombre,
            $correo,
            $genero,
            $tipo_documento,
            $numero_documento,
            $rol
        );
        $stmt->execute();

        $id_nuevo = $stmt->insert_id;
        responder(true, "Usuario creado", [
            'usuario' => [
                'id'               => $id_nuevo,
                'nombre'           => $nombre,
                'correo'           => $correo,
                'genero'           => $genero,
                'tipo_documento'   => $tipo_documento,
                'numero_documento' => $numero_documento,
                'rol'              => $rol
            ]
        ]);
        break;

    case 'PUT':
        // Actualizar un usuario existente
        $input = json_decode(file_get_contents("php://input"), true);
        $id              = intval($input['id']              ?? 0);
        $nombre          = $input['nombre']                 ?? '';
        $correo          = $input['correo']                 ?? '';
        $genero          = $input['genero']                 ?? '';
        $tipo_documento  = $input['tipo_documento']         ?? '';
        $numero_documento= $input['numero_documento']       ?? '';
        $rol             = intval($input['rol']             ?? 0);

        if (!$id || !$nombre || !$correo || !$rol) {
            responder(false, "Faltan campos requeridos");
        }

        $sql = "UPDATE usuarios SET 
                    nombre_completo = ?, 
                    nombre_usuario  = ?, 
                    genero          = ?, 
                    tipo_documento  = ?, 
                    numero_documento= ?, 
                    id_rol          = ?
                WHERE id_usuario   = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssi",
            $nombre,
            $correo,
            $genero,
            $tipo_documento,
            $numero_documento,
            $rol,
            $id
        );
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            responder(true, "Usuario actualizado");
        } else {
            responder(false, "No se actualizó ningún registro");
        }
        break;

    case 'DELETE':
        // Eliminar un usuario por su ID
        $input = json_decode(file_get_contents("php://input"), true);
        $id    = intval($input['id'] ?? 0);
        if (!$id) {
            responder(false, "ID inválido o faltante");
        }

        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            responder(true, "Usuario eliminado");
        } else {
            responder(false, "No se encontró usuario con ese ID");
        }
        break;

    default:
        // Método no soportado
        responder(false, "Método no soportado: " . $_SERVER['REQUEST_METHOD']);
}