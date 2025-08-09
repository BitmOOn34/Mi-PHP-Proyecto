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

// Codificación
$conn->set_charset("utf8mb4");
?>
