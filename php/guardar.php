<?php
session_start();
require __DIR__ . '/../config/config.php';//Cargar la configuración de la base de datos

//Verificación de uso del metodo POST del formulario
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //si no se enviar por POST se envia al nuevo formulario de registro
    header('Location: ../public/Registrousuarios.html');
    exit;
}
//Se toman los datos del formulario
$email    = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);//Validacion de correo
$pass     = $_POST['password'];
$confirm  = $_POST['confirmar'];
$rol      = $_POST['rol'];
$estado   = $_POST['estado'];
$nombreCompleto = $_POST['nombre'];
$genero = $_POST['genero'];
$tipo_doc = $_POST['tipo_doc'];
$numero_doc = $_POST['numero_doc'];

//Si el correo est mal escrito genera error 
if (!$email) {
    die("Email no válido.");
}//Si las contraseñas no son iguales genera error
if ($pass !== $confirm) {
    die("Las contraseñas no coinciden.");
}
//Valida si el correo ya esta registrado en la base de datos
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE nombre_usuario = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    die("Ese email ya está registrado.");//Mensaje de error si ya esta registrado el correo
}
//Convierte la contraseña en codigo seguto - hash para protegerla
$hash = password_hash($pass, PASSWORD_DEFAULT);

//Creacion del nuevo usuario en la tabla "usuarios"
$sql = "INSERT INTO usuarios 
        (id_rol, nombre_usuario, fecha_registro, clave_acceso, estado, nombre_completo, genero, tipo_documento, numero_documento)
        VALUES
        (:rol, :email, NOW(), :hash, :estado, :nombre, :genero, :tipo_doc, :numero_doc)";
$stmt = $pdo->prepare($sql);//Prepara la consulta SQL
$stmt->execute([//Llena lo campos vacios
    ':rol'    => $rol,
    ':email'  => $email,
    ':hash'   => $hash,
    ':estado' => $estado,
    ':nombre' => $nombreCompleto,
    ':genero' => $genero,
    ':tipo_doc' => $tipo_doc,
    ':numero_doc' => $numero_doc
]);

//Como el usuario que se registra tiene rol cliente "2" se guardara tambien en la tabla clientes
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

// ✅ MOSTRAR MENSAJE DE ÉXITO SI TODO ES OK
echo "¡OK!";
exit;

