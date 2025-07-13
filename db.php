<?php
$host = "sql309.infinityfree.com";
$usuario = "if0_38973658";
$contrasena = "";
$basededatos = "if0_38973658_resetas";

$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

// Verifica la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Establece codificación UTF-8 para evitar problemas con caracteres especiales
$conn->set_charset("utf8mb4");

echo "Conexión exitosa";
?>
