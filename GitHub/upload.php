<?php

// Maneja la subida, edición y eliminación de recetas con o sin imagen

include 'db.php';

/**
 * Sube una imagen al servidor y devuelve el nombre del archivo.
 * 
 * @param array|null $file
 * @return string|null
 * @throws Exception
 */
function uploadImage($file) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $uploadDir = 'uploads/';
    
    // Crear el directorio si no existe
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $originalName = basename($file['name']);
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

    // Validar formato de imagen
    if (!in_array($ext, $allowedExt)) {
        throw new Exception("Formato de imagen no permitido.");
    }

    $newFileName = uniqid('img_') . '.' . $ext;
    $destPath = $uploadDir . $newFileName;

    // Mover archivo subido
    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        throw new Exception("Error al subir la imagen.");
    }

    return $newFileName;
}

/**
 * Ejecuta una consulta preparada de forma segura.
 * 
 * @param mysqli $conn
 * @param string $sql
 * @param string $types
 * @param mixed ...$params
 * @throws Exception
 */
function executeQuery($conn, $sql, $types,
