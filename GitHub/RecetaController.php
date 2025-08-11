<?php
// Incluir archivos necesarios
require_once 'db.php';
require_once 'RecetaModel.php';

$model = new RecetaModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario con sanitización básica
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
    $usuario_id = isset($_POST['usuario_id']) ? (int)$_POST['usuario_id'] : null;

    $nombre = trim($_POST['nombre'] ?? '');
    $ingredientes = trim($_POST['ingredientes'] ?? '');
    $instrucciones = trim($_POST['instrucciones'] ?? '');
    $tiempo = trim($_POST['tiempo_preparacion'] ?? '');
    $porciones = isset($_POST['porciones']) ? (int)$_POST['porciones'] : 1;
    $categoria = trim($_POST['categoria'] ?? '');

    $rutaImagen = null;

    // Manejo de imagen subida
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        // Generar nombre único para evitar colisiones
        $nombreArchivo = uniqid() . '-' . basename($_FILES['imagen']['name']);
        $rutaImagen = $uploadDir . $nombreArchivo;

        // Mover archivo al directorio de destino
        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaImagen)) {
            // Opcional: manejar error de subida
            die("Error al subir la imagen.");
        }
    }

    // Validar datos mínimos
    if (empty($nombre) || empty($ingredientes) || empty($instrucciones)) {
        die("Por favor completa todos los campos requeridos.");
    }

    // Procesar acción según tipo
    switch ($action) {
        case 'add':
            $model->agregarReceta($usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $rutaImagen);
            break;

        case 'update':
            if (!$id) {
                die("ID de receta no proporcionado para actualizar.");
            }
            $model->actualizarReceta($id, $usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $rutaImagen);
            break;

        case 'delete':
            if (!$id) {
                die("ID de receta no proporcionado para eliminar.");
            }
            $model->eliminarReceta($id, $usuario_id);
            break;

        default:
            die("Acción no válida.");
    }
}

// Redirigir al inicio después de procesar
header("Location: index.php");
exit;
