<?php

// Funciones para renderizar comentarios y recetas con sus formularios y calificaciones

/**
 * Muestra una lista de comentarios debajo de una receta.
 *
 * @param array $comentarios
 */
function renderComentarios($comentarios) {
    if (count($comentarios) === 0) {
        echo "<p>No hay comentarios aún.</p>";
        return;
    }

    foreach ($comentarios as $comentario) {
        echo "<p><b>" . htmlspecialchars($comentario['autor']) . "</b> (" . htmlspecialchars($comentario['fecha']) . "):<br>" .
             nl2br(htmlspecialchars($comentario['contenido'])) . "</p><hr>";
    }
}

/**
 * Muestra una fila de la tabla con los datos de una receta,
 * incluyendo acciones de edición, calificación y comentarios.
 *
 * @param array $row Datos de la receta
 * @param string $rol Rol del usuario actual
 * @param int $usuario_id ID del usuario actual
 * @param object $comentarioModel Instancia del modelo de comentarios
 */
function renderRecetaFila($row, $rol, $usuario_id, $comentarioModel) {
    ?>
    <tr id="receta-<?= $row['id'] ?>">
        <!-- Imagen -->
        <td data-label="Imagen">
            <?php if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])): ?>
                <img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" width="100" />
            <?php else: ?>
                <span class="sin-imagen">Sin imagen</span>
            <?php endif; ?>
        </td>

        <!-- Datos básicos -->
        <td data-label="Nombre"><?= htmlspecialchars($row['nombre']) ?></td>
        <td data-label="Categoría"><?= htmlspecialchars($row['categoria']) ?></td>
        <td data-label="Ingredientes"><?= nl2br(htmlspecialchars($row['ingredientes'])) ?></td>
        <td data-label="Instrucciones"><?= nl2br(htmlspecialchars($row['instrucciones'])) ?></td>
        <td data-label="Tiempo"><?= htmlspecialchars($row['tiempo_preparacion']) ?></td>
        <td data-label="Porciones"><?= htmlspecialchars($row['porciones']) ?></td>

        <!-- Calificación -->
        <td data-label="Calificación">
            <div class="calificacion-container">
                <span><?= number_format($row['calificacion'], 1) ?> / 5</span>
                
                <?php if ($rol !== 'invitado'): ?>
                    <form action="calificar.php" method="POST" style="display: flex; gap: 4px;">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                        <select name="calificacion" onchange="this.form.submit()" class="rating-select">
                            <option value="">Calificar</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?= $i ?>" <?= $row['calificacion'] == $i ? 'selected' : '' ?>>
                                    <?= $i ?> ⭐
                                </option>
                            <?php endfor; ?>
                        </select>
                    </form>
                <?php endif; ?>
            </div>
        </td>

        <!-- Botón de edición (solo para el autor de la receta) -->
        <td data-label="Acciones">
            <?php if ($rol !== 'invitado' && $usuario_id == $row['usuario_id']): ?>
                <button onclick='editRecord(
                    <?= json_encode($row['id']) ?>,
                    <?= json_encode($row['nombre']) ?>,
                    <?= json_encode($row['ingredientes']) ?>,
                    <?= json_encode($row['instrucciones']) ?>,
                    <?= json_encode($row['tiempo_preparacion']) ?>,
                    <?= json_encode($row['porciones']) ?>,
                    <?= json_encode($row['categoria']) ?>
                )'>Editar</button>
            <?php endif; ?>
        </td>
    </tr>

    <!-- Fila de comentarios -->
    <tr>
        <td colspan="9" class="comentario-fila">
            <strong>Comentarios:</strong><br />

            <?php
            // Mostrar comentarios asociados a la receta
            $comentarios = $comentarioModel->getComentariosPorReceta($row['id']);
            renderComentarios($comentarios);
            ?>

            <!-- Formulario de nuevo comentario -->
            <?php if ($rol !== 'invitado'): ?>
                <form action="comentarios.php" method="POST">
                    <input type="hidden" name="receta_id" value="<?= $row['id'] ?>" />
                    <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>" />
                    <input type="hidden" name="autor" value="<?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Invitado') ?>" />

                    <label for="contenido_<?= $row['id'] ?>">Comentario:</label><br />
                    <textarea id="contenido_<?= $row['id'] ?>" name="contenido" rows="3" required></textarea><br />
                    <button type="submit">Agregar comentario</button>
                </form>
            <?php else: ?>
                <p><em>Inicia sesión para comentar.</em></p>
            <?php endif; ?>
        </td>
    </tr>
    <?php
}
?>
