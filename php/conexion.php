<?php
require __DIR__ . '/vendor/autoload.php'; // Carga Composer

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // Ruta donde está tu .env
$dotenv->load();

// Acceder a las variables de entorno
$host = $_ENV['DB_HOST'];
$usuario = $_ENV['DB_USER'];
$contrasena = $_ENV['DB_PASSWORD'];
$base_de_datos = $_ENV['DB_NAME'];

// Crear la conexión
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar la conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
