<h2>Recetas</h2>
<table>
    <thead>
        <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Ingredientes</th>
            <th>Instrucciones</th>
            <th>Tiempo</th>
            <th>Porciones</th>
            <th>Calificación</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        if (empty($recetas)) {
            echo '<tr><td colspan="9" class="alert-red">No se encontró ninguna receta que coincida con tu búsqueda.</td></tr>';
        } else {
            foreach ($recetas as $row) {
                renderRecetaFila($row, $rol, $usuario_id, $comentarioModel);
            }
        }
        ?>
    </tbody>
</table>
