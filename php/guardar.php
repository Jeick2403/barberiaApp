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

// ✅ MOSTRAR MENSAJE DE ÉXITO
echo "¡OK!";
exit;