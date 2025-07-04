<?php
// php/guardar.php
session_start();
require __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../public/Registrousuarios.html');
    exit;
}

// 1. Recoger y sanear datos
$email    = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$pass     = $_POST['password'];
$confirm  = $_POST['confirmar'];
$rol      = $_POST['rol']; // ← CAMBIO AQUÍ: ahora se toma el número del formulario
$estado   = $_POST['estado'];

if (!$email) {
    die("Email no válido. <a href='../public/Registrousuarios.html'>Volver</a>");
}
if ($pass !== $confirm) {
    die("Las contraseñas no coinciden. <a href='../public/Registrousuarios.html'>Volver</a>");
}

// 2. Comprobar si ya existe ese email
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("Ese email ya está registrado. <a href='../public/Registrousuarios.html'>Volver</a>");
}

// 3. Hashear la contraseña
$hash = password_hash($pass, PASSWORD_DEFAULT);

// 4. Insertar en la base de datos
$sql = "INSERT INTO usuarios 
        (id_rol, nombre_usuario, fecha_registro, clave_acceso, estado)
        VALUES
        (:rol, :email, NOW(), :hash, :estado)";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':rol'    => $rol,
    ':email'  => $email,
    ':hash'   => $hash,
    ':estado' => $estado
]);

// 5. Redirigir al login
header('Location: ../public/login.html');
exit;
