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
 * @param mysqli $conn Conexión a la base de datos.
 * @param string $sql Consulta SQL con placeholders (?).
 * @param string $types Tipos de datos de los parámetros (ej. "ssi").
 * @param mixed ...$params Parámetros variables a enlazar en la consulta.
 * @return mysqli_stmt|false El statement ejecutado o false si falla.
 * @throws Exception Si hay un error al preparar o ejecutar la consulta.
 */
function executeQuery($conn, $sql, $types, ...$params) {
    // Preparar la consulta
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Enlazar parámetros si existen
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }

    return $stmt;
}

