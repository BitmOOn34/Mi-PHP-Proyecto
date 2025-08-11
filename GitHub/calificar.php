<?php
include 'db.php';
include 'RecetaModel.php';

if (isset($_POST['id'], $_POST['calificacion'])) {
    $id = intval($_POST['id']);
    $calificacion = floatval($_POST['calificacion']);

    $model = new RecetaModel($conn);
    $model->actualizarCalificacion($id, $calificacion);
}

$conn->close();
header("Location: index.php");
exit;
?>
