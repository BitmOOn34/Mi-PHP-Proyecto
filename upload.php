<?php
include 'db.php';

$action = $_POST['action'];
$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$ingredientes = $_POST['ingredientes'] ?? '';
$instrucciones = $_POST['instrucciones'] ?? '';
$tiempo_preparacion = $_POST['tiempo_preparacion'] ?? '';
$porciones = $_POST['porciones'] ?? 0;
$categoria = $_POST['categoria'] ?? '';
$imagen_nombre = null;

// Manejar la imagen si se subió
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/';
    $tmpName = $_FILES['imagen']['tmp_name'];
    $originalName = basename($_FILES['imagen']['name']);
    $ext = pathinfo($originalName, PATHINFO_EXTENSION);
    $newFileName = uniqid() . '.' . $ext;
    $destPath = $uploadDir . $newFileName;

    if (move_uploaded_file($tmpName, $destPath)) {
        $imagen_nombre = $newFileName;
    } else {
        die("Error al subir la imagen.");
    }
}

// Ahora procesar según acción
if ($action == 'add') {
    $sql = "INSERT INTO recetas (nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisds", $nombre, $ingredientes, $instrucciones, $tiempo_preparacion, $porciones, $categoria, $imagen_nombre);
    $stmt->execute();
    $stmt->close();
} elseif ($action == 'update') {
    if ($imagen_nombre) {
        // Actualiza con nueva imagen
        $sql = "UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=?, imagen=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisdsi", $nombre, $ingredientes, $instrucciones, $tiempo_preparacion, $porciones, $categoria, $imagen_nombre, $id);
    } else {
        // Actualiza sin cambiar imagen
        $sql = "UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssisdi", $nombre, $ingredientes, $instrucciones, $tiempo_preparacion, $porciones, $categoria, $id);
    }
    $stmt->execute();
    $stmt->close();
} elseif ($action == 'delete') {
    $sql = "DELETE FROM recetas WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit;
?>
