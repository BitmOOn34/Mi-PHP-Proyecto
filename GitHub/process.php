<?php
include 'db.php';
session_start();

$usuario_id = $_SESSION['usuario_id'] ?? null;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

function verificarPropietario($conn, $id, $usuario_id) {
    $stmt = $conn->prepare("SELECT usuario_id FROM recetas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($owner_id);
    $stmt->fetch();
    $stmt->close();
    
    return $owner_id === $usuario_id;
}

$action = $_POST['action'];
$id = $_POST['id'] ?? null;
$nombre = $_POST['nombre'] ?? '';
$ingredientes = $_POST['ingredientes'] ?? '';
$instrucciones = $_POST['instrucciones'] ?? '';
$tiempo = $_POST['tiempo_preparacion'] ?? '';
$porciones = $_POST['porciones'] ?? 1;
$categoria = $_POST['categoria'] ?? '';
$imagen = null;

if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $nombreImagen = time() . '_' . basename($_FILES['imagen']['name']);
    $rutaImagen = $uploadDir . $nombreImagen;
    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
        $imagen = $nombreImagen;
    }
}

switch ($action) {
    case 'add':
        if (!$usuario_id) {
            die('Error: Debes iniciar sesión para agregar una receta.');
        }
        $stmt = $conn->prepare("INSERT INTO recetas (nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen, usuario_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssissi", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen, $usuario_id);
        $stmt->execute();
        $stmt->close();
        break;

    case 'update':
        if (!$usuario_id || !$id || !verificarPropietario($conn, $id, $usuario_id)) {
            die('Error: No tienes permiso para editar esta receta.');
        }
        if ($imagen) {
            $stmt = $conn->prepare("UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=?, imagen=? WHERE id=?");
            $stmt->bind_param("ssssissi", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen, $id);
        } else {
            $stmt = $conn->prepare("UPDATE recetas SET nombre=?, ingredientes=?, instrucciones=?, tiempo_preparacion=?, porciones=?, categoria=? WHERE id=?");
            $stmt->bind_param("ssssisi", $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $id);
        }
        $stmt->execute();
        $stmt->close();
        break;

    case 'delete':
        if (!$usuario_id || !$id || !verificarPropietario($conn, $id, $usuario_id)) {
            die('Error: No tienes permiso para eliminar esta receta.');
        }
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
