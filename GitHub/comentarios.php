<?php
session_start();
include 'db.php';

// Verificamos si el usuario está logueado y obtenemos el nombre del usuario de la sesión
if (isset($_SESSION['usuario_nombre'])) {
    $autor = $_SESSION['usuario_nombre'];  // Tomamos el nombre del usuario de la sesión
    $usuario_id = $_SESSION['usuario_id'];  // Tomamos el ID del usuario de la sesión
} else {
    $autor = 'Invitado';  // Si el usuario no está logueado, lo marcamos como invitado
    $usuario_id = null;  // Si no está logueado, dejamos el usuario_id como NULL
}

// Verificamos si los datos necesarios están en el formulario
if (isset($_POST['receta_id'], $_POST['contenido'])) {
    $receta_id = intval($_POST['receta_id']);
    $contenido = trim($_POST['contenido']);
    $fecha = date('Y-m-d H:i:s');

    // Validar datos mínimos
    if ($contenido !== '') {
        // Preparamos y ejecutamos la inserción del comentario en la base de datos
        $stmt = $conn->prepare("INSERT INTO comentarios (receta_id, autor, contenido, fecha, usuario_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("isssi", $receta_id, $autor, $contenido, $fecha, $usuario_id);  // Incluimos el usuario_id
        $stmt->execute();
        $stmt->close();
    }
}

$conn->close();

// Redirigir a la página principal
header("Location: index.php");
exit;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comentarios</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h3>Comentarios</h3>
    <form action="comentarios.php" method="POST">
        <input type="hidden" name="receta_id" value="ID_DE_LA_RECETA">  <!-- Cambia esto según cómo manejas la ID de la receta -->
        <textarea name="contenido" placeholder="Escribe tu comentario..." required></textarea>
        <button type="submit">Agregar comentario</button>
    </form>
</body>
</html>
