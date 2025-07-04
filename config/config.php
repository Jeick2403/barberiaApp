<?php
// config/config.php

$host = '127.0.0.1';         // o 'localhost'
$db = 'sistema_barberia';    // el nombre exacto de tu base de datos
$user = 'root';              // por defecto en XAMPP es 'root'
$pass = '';                  // por defecto en XAMPP no hay contraseña
$charset = 'utf8mb4';        // conjunto de caracteres correcto

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo 'Error de conexión a la base de datos: ' . $e->getMessage();
    exit;
}
