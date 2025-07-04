<?php
// public/login.php
session_start();
require __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.html');
    exit;
}

$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$pass  = $_POST['password'];

if (!$email) {
    die("Email no válido. <a href='login.html'>Volver</a>");
}

// 1. Recuperar usuario
$stmt = $pdo->prepare("
   SELECT id_usuario, nombre_usuario, clave_acceso, estado
   FROM usuarios
   WHERE nombre_usuario = ?
");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Usuario no encontrado. <a href='login.html'>Volver</a>");
}
if ((int)$user['estado'] !== 1) {
    die("Usuario inactivo. Contacta al administrador.");
}

// 2. Verificar contraseña
if (!password_verify($pass, $user['clave_acceso'])) {
    die("Contraseña incorrecta. <a href='login.html'>Volver</a>");
}

// 3. Crear sesión
$_SESSION['usuario_id']     = $user['id_usuario'];
$_SESSION['usuario_email']  = $user['nombre_usuario'];

// 4. Redirigir a área privada
header('Location: dashboard.php');
exit;
