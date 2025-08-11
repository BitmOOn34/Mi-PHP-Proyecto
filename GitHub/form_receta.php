<?php if ($rol !== 'invitado'): ?>
    <!-- Formulario para agregar, actualizar o eliminar una receta -->
    <form action="process.php" method="POST" enctype="multipart/form-data">
        <!-- Campo oculto para el ID de la receta (se usa en actualización o eliminación) -->
        <input type="hidden" name="id" id="id" />

        <!-- Campo oculto para el ID del usuario que envía la receta -->
        <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>" />

        <!-- Nombre de la receta -->
        <label for="nombre">Nombre de la receta:</label>
        <input type="text" name="nombre" id="nombre" required />

        <!-- Ingredientes -->
        <label for="ingredientes">Ingredientes:</label>
        <textarea name="ingredientes" id="ingredientes" rows="4" required></textarea>

        <!-- Instrucciones -->
        <label for="instrucciones">Instrucciones:</label>
        <textarea name="instrucciones" id="instrucciones" rows="4" required></textarea>

        <!-- Tiempo de preparación -->
        <label for="tiempo_preparacion">Tiempo de preparación:</label>
        <input type="text" name="tiempo_preparacion" id="tiempo_preparacion" required />

        <!-- Porciones -->
        <label for="porciones">Porciones:</label>
        <input type="number" name="porciones" id="porciones" min="1" required />

        <!-- Categoría -->
        <label for="categoria">Categoría:</label>
        <input type="text" name="categoria" id="categoria" placeholder="Ej. Postre, Ensalada" required />

        <!-- Imagen de la receta -->
        <label for="imagen">Imagen de la receta:</label>
        <input type="file" name="imagen" id="imagen" accept="image/*" />

        <!-- Botones de acción -->
        <button type="submit" name="action" value="add">Agregar</button>
        <button type="submit" name="action" value="update">Actualizar</button>
        <button type="submit" name="action" value="delete" onclick="return confirm('¿Estás seguro de que deseas eliminar esta receta?');">Borrar</button>
    </form>
<?php else: ?>
    <!-- Mensaje para usuarios invitados -->
    <p class="alert-red">Inicia sesión para poder agregar o editar recetas.</p>
<?php endif; ?>
