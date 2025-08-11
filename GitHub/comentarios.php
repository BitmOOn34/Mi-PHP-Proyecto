<?php
session_start();

require_once 'db.php';
require_once 'ComentarioModel.php';

// Obtener datos del usuario desde la sesión
$autor = $_SESSION['usuario_nombre'] ?? 'Invitado';
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Validar datos POST obligatorios
if (empty($_POST['receta_id']) || empty(trim($_POST['contenido']))) {
    header("Location: index.php");
    exit;
}

$receta_id = intval($_POST['receta_id']);
$contenido = trim($_POST['contenido']);
$fecha = date('Y-m-d H:i:s');

// Verificar que la receta exista antes de insertar el comentario
$stmt = $conn->prepare("SELECT id FROM recetas WHERE id = ?");
$stmt->bind_param("i", $receta_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    $stmt->close();
    die("Error: La receta no existe.");
}
$stmt->close();

// Crear instancia del modelo y agregar el comentario
$comentarioModel = new ComentarioModel($conn);

$exito = $comentarioModel->agregarComentario(
    $receta_id,
    $autor,
    $contenido,
    $fecha,
    $usuario_id
);

if (!$exito) {
    die("Error: No se pudo agregar el comentario.");
}

// Cerrar la conexión y redirigir a la receta comentada
$conn->close();
header("Location: index.php#receta-" . urlencode($receta_id));
exit;
?>
