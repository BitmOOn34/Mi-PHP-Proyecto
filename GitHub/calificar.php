<?php
include 'db.php';

if (isset($_POST['id'], $_POST['calificacion'])) {
    $id = intval($_POST['id']);
    $calificacion = intval($_POST['calificacion']);

    // Validar que la calificación esté entre 1 y 5
    if ($calificacion >= 1 && $calificacion <= 5) {
        // Actualizar la calificación en la tabla recetas
        $stmt = $conn->prepare("UPDATE recetas SET calificacion = ? WHERE id = ?");
        $stmt->bind_param("ii", $calificacion, $id);
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// Volver a la página principal
header("Location: index.php");
exit;
?>
