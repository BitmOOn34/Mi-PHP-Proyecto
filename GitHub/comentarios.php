<?php
include 'db.php';

if (isset($_POST['receta_id'], $_POST['autor'], $_POST['contenido'])) {
    $receta_id = intval($_POST['receta_id']);
    $autor = trim($_POST['autor']);
    $contenido = trim($_POST['contenido']);
    $fecha = date('Y-m-d H:i:s');

    // Validar datos mínimos
    if ($autor !== '' && $contenido !== '') {
        $stmt = $conn->prepare("INSERT INTO comentarios (receta_id, autor, contenido, fecha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $receta_id, $autor, $contenido, $fecha);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// Redirigir a la página principal
header("Location: index.php");
exit;
?>
