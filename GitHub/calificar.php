<?php
include 'db.php';

if (isset($_POST['id'], $_POST['calificacion'])) {
    $id = intval($_POST['id']);
    $calificacion = floatval($_POST['calificacion']); // Usamos float para permitir calificaciones con decimales

    // Validar que la calificación esté entre 1 y 5
    if ($calificacion >= 1 && $calificacion <= 5) {
        // Actualizar la calificación en la tabla recetas
        $stmt = $conn->prepare("UPDATE recetas SET calificacion = ? WHERE id = ?");
        $stmt->bind_param("di", $calificacion, $id); // 'd' para decimal, 'i' para entero
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// Volver a la página principal
header("Location: index.php");
exit;
?>
