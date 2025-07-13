<?php
include 'db.php';

// Crear carpeta "uploads" si no existe
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$action       = $_POST['action'];
$id           = $_POST['id'] ?? null;
$nombre       = $_POST['nombre'] ?? '';
$ingredientes = $_POST['ingredientes'] ?? '';
$instrucciones= $_POST['instrucciones'] ?? '';
$tiempo       = $_POST['tiempo_preparacion'] ?? '';
$porciones    = $_POST['porciones'] ?? 1;
$categoria    = $_POST['categoria'] ?? '';
$imagen       = null;

// Subida de imagen
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombreImagen = time() . '_' . basename($_FILES['imagen']['name']);
    $rutaImagen = $uploadDir . $nombreImagen;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
        $imagen = $nombreImagen;
    }
}

switch ($action) {
    case 'add':
        $stmt = $conn->prepare("INSERT INTO recetas (nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen) VALUES (?, ?, ?, ?, ?, ?, ?)");
        // tipos: nombre,s, ingredientes,s, instrucciones,s, tiempo_preparacion,s, porciones,i, categoria,s, imagen,s
        $stmt->bind_param("ssssiss", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen);
        $stmt->execute();
        $stmt->close();
        break;

    case 'update':
        if ($imagen) {
            // Con imagen nueva
            $stmt = $conn->prepare("UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=?, imagen=? WHERE id=?");
            // tipos: s, s, s, s, i, s, s, i
            $stmt->bind_param("ssssissi", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen, $id);
        } else {
            // Sin imagen nueva
            $stmt = $conn->prepare("UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=? WHERE id=?");
            // tipos: s, s, s, s, i, s, i
            $stmt->bind_param("ssssisi", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $id);
        }
        $stmt->execute();
        $stmt->close();
        break;

    case 'delete':
        // Eliminar receta e imagen si existe
        $res = $conn->query("SELECT imagen FROM recetas WHERE id = $id");
        if ($res && $row = $res->fetch_assoc()) {
            if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])) {
                unlink("uploads/" . $row['imagen']);
            }
        }
        $stmt = $conn->prepare("DELETE FROM recetas WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        break;
}

$conn->close();
header("Location: index.php");
exit;
?>
