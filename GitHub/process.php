<?php
session_start();

require_once 'db.php';
require_once 'RecetaModel.php';

/**
 * Función para terminar la ejecución mostrando un mensaje de error.
 */
function abortWithError(string $msg): void {
    die($msg);
}

// Verificar que el usuario esté autenticado
$usuario_id = $_SESSION['usuario_id'] ?? null;
if (!$usuario_id) {
    abortWithError("Debes iniciar sesión para realizar esta acción.");
}

// Directorio para guardar imágenes subidas
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$model = new RecetaModel($conn);

// Recoger datos del formulario
$action = $_POST['action'] ?? '';
$id = isset($_POST['id']) ? (int)$_POST['id'] : null;
$nombre = trim($_POST['nombre'] ?? '');
$ingredientes = trim($_POST['ingredientes'] ?? '');
$instrucciones = trim($_POST['instrucciones'] ?? '');
$tiempo = trim($_POST['tiempo_preparacion'] ?? '');
$porciones = isset($_POST['porciones']) ? (int)$_POST['porciones'] : 1;
$categoria = trim($_POST['categoria'] ?? '');
$imagen = null;

// Manejar la imagen subida (si existe)
try {
    $imagen = $model->manejarImagenSubida($_FILES['imagen'] ?? null, $uploadDir);
} catch (Exception $e) {
    abortWithError("Error al subir imagen: " . $e->getMessage());
}

/**
 * Verifica que el usuario sea propietario de la receta.
 * Termina la ejecución si no tiene permiso.
 */
function checkOwnershipOrAbort(RecetaModel $model, ?int $id, int $usuario_id): void {
    if (!$id) {
        abortWithError("ID de receta no proporcionado.");
    }
    if (!$model->usuarioEsPropietario($id, $usuario_id)) {
        abortWithError("No tienes permiso para modificar esta receta.");
    }
}

// Ejecutar la acción solicitada
switch ($action) {
    case 'add':
        $model->agregarReceta($usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen);
        break;

    case 'update':
        checkOwnershipOrAbort($model, $id, $usuario_id);
        $model->actualizarReceta($id, $usuario_id, $nombre, $ingredientes, $instrucciones, $tiempo, $porciones, $categoria, $imagen);
        break;

    case 'delete':
        checkOwnershipOrAbort($model, $id, $usuario_id);
        $model->eliminarReceta($id, $usuario_id);
        break;

    default:
        abortWithError("Acción inválida.");
}

// Cerrar conexión y redirigir al index
$conn->close();
header("Location: index.php");
exit;
