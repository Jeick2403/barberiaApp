<?php
session_start();
require __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/Registrousuarios.html');
    exit;
}

$email    = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$pass     = $_POST['password'];
$confirm  = $_POST['confirmar'];
$rol      = $_POST['rol'];
$estado   = $_POST['estado'];
$nombreCompleto = $_POST['nombre'];
$genero = $_POST['genero'];
$tipo_doc = $_POST['tipo_doc'];
$numero_doc = $_POST['numero_doc'];


if (!$email) {
    die("Email no válido.");
}
if ($pass !== $confirm) {
    die("Las contraseñas no coinciden.");
}

$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("Ese email ya está registrado.");
}

$hash = password_hash($pass, PASSWORD_DEFAULT);

$sql = "INSERT INTO usuarios 
        (id_rol, nombre_usuario, fecha_registro, clave_acceso, estado, nombre_completo, genero, tipo_documento, numero_documento)
        VALUES
        (:rol, :email, NOW(), :hash, :estado, :nombre, :genero, :tipo_doc, :numero_doc)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':rol'    => $rol,
    ':email'  => $email,
    ':hash'   => $hash,
    ':estado' => $estado,
    ':nombre' => $nombreCompleto,
    ':genero' => $genero,
    ':tipo_doc' => $tipo_doc,
    ':numero_doc' => $numero_doc
]);

if ($rol == 2) {
    $idUsuario = $pdo->lastInsertId(); // obtiene el ID del usuario recién creado
    $sqlCliente = "INSERT INTO clientes (id_usuario, nombre_completo, genero, tipo_documento, numero_documento, nombre_usuario) 
               VALUES (:id_usuario, :nombre, :genero, :tipo_doc, :numero_doc, :nombre_usuario)";
    $stmtCliente = $pdo->prepare($sqlCliente);
    $stmtCliente->execute([
        ':id_usuario' => $idUsuario,
        ':nombre'     => $nombreCompleto,
        ':genero'     => $genero,
        ':tipo_doc'   => $tipo_doc,
        ':numero_doc' => $numero_doc,
        ':nombre_usuario' => $email
]);
  }

// ✅ MOSTRAR MENSAJE DE ÉXITO
echo "¡OK!";
exit;