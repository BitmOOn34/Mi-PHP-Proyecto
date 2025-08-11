<?php
/**
 * Usando mysqli con configuración UTF-8 y control de errores.
 */

// Parámetros de conexión
$host = "sql309.infinityfree.com";
$usuario = "if0_38973658";
$contrasena = "";
$basededatos = "if0_38973658_resetas";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $basededatos);

// Verificar la conexión
if ($conn->connect_error) {
    // No mostrar detalles técnicos en producción por seguridad
    die("Error de conexión a la base de datos.");
}

// Establecer el conjunto de caracteres a UTF-8 para soportar acentos y caracteres especiales
$conn->set_charset("utf8mb4");
?>
