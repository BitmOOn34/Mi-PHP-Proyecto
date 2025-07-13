<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>RECETAS PARA TU COCINA</title>
    <link rel="icon" type="image/png" href="fondos/icono.png" />
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
    <div class="container">
        <h1>RECETAS PARA TU COCINA</h1>

        <!-- Imagen de portada -->
        <div class="imagen-portada">
            <img src="fondos/Recetas.avif" alt="Imagen de presentación de recetas" />
        </div>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o ingrediente..." />
            <button type="submit">Buscar</button>
        </form>

        <!-- Formulario para agregar/editar recetas -->
        <form action="process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" />

            <label for="nombre">Nombre de la receta:</label>
            <input type="text" name="nombre" id="nombre" required />

            <label for="ingredientes">Ingredientes:</label>
            <textarea name="ingredientes" id="ingredientes" rows="4" required></textarea>

            <label for="instrucciones">Instrucciones:</label>
            <textarea name="instrucciones" id="instrucciones" rows="4" required></textarea>

            <label for="tiempo_preparacion">Tiempo de preparación:</label>
            <input type="text" name="tiempo_preparacion" id="tiempo_preparacion" required />

            <label for="porciones">Porciones:</label>
            <input type="number" name="porciones" id="porciones" required />

            <label for="categoria">Categoría:</label>
            <input type="text" name="categoria" id="categoria" placeholder="Ej. Postre, Ensalada" required />

            <label for="imagen">Imagen de la receta:</label>
            <input type="file" name="imagen" id="imagen" />

            <button type="submit" name="action" value="add">Agregar</button>
            <button type="submit" name="action" value="update">Actualizar</button>
            <button type="submit" name="action" value="delete">Borrar</button>
        </form>

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
                include 'db.php';
                $filtro = isset($_GET['busqueda']) ? $_GET['busqueda'] : '';
                $sql = "SELECT * FROM recetas WHERE nombre LIKE '%$filtro%' OR ingredientes LIKE '%$filtro%'";
                $result = $conn->query($sql);

                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td><img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" alt="" width="100" /></td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['categoria']) ?></td>
                    <td><?= htmlspecialchars($row['ingredientes']) ?></td>
                    <td><?= htmlspecialchars($row['instrucciones']) ?></td>
                    <td><?= htmlspecialchars($row['tiempo_preparacion']) ?></td>
                    <td><?= htmlspecialchars($row['porciones']) ?></td>
                    <td>
                        <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 6px;">
                            <span><?= number_format($row['calificacion'], 1) ?> / 5</span>
                            <form action="calificar.php" method="POST" style="display: flex; gap: 4px;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                <select name="calificacion" onchange="this.form.submit()" style="padding: 4px; border-radius: 6px;">
                                    <option value="">Calificar</option>
                                    <option value="1">1 ⭐</option>
                                    <option value="2">2 ⭐</option>
                                    <option value="3">3 ⭐</option>
                                    <option value="4">4 ⭐</option>
                                    <option value="5">5 ⭐</option>
                                </select>
                            </form>
                        </div>
                    </td>
                    <td>
                        <button onclick='editRecord(
                            <?= json_encode($row['id']) ?>,
                            <?= json_encode($row['nombre']) ?>,
                            <?= json_encode($row['ingredientes']) ?>,
                            <?= json_encode($row['instrucciones']) ?>,
                            <?= json_encode($row['tiempo_preparacion']) ?>,
                            <?= json_encode($row['porciones']) ?>,
                            <?= json_encode($row['categoria']) ?>
                        )'>Editar</button>
                    </td>
                </tr>
                <tr>
                    <td colspan="9" style="background:#fafafa; text-align:left;">
                        <strong>Comentarios:</strong><br />
                        <?php
                        $idReceta = $row['id'];
                        $comentariosSql = "SELECT * FROM comentarios WHERE receta_id = $idReceta ORDER BY fecha DESC";
                        $comentariosResult = $conn->query($comentariosSql);

                        if ($comentariosResult->num_rows > 0) {
                            while ($comentario = $comentariosResult->fetch_assoc()) {
                                echo "<p><b>" . htmlspecialchars($comentario['autor']) . "</b> (" . $comentario['fecha'] . "):<br>" .
                                     nl2br(htmlspecialchars($comentario['contenido'])) . "</p><hr>";
                            }
                        } else {
                            echo "<p>No hay comentarios aún.</p>";
                        }
                        ?>
                        <form action="comentarios.php" method="POST">
                            <input type="hidden" name="receta_id" value="<?= $row['id'] ?>" />
                            <label for="autor_<?= $row['id'] ?>">Nombre:</label><br />
                            <input type="text" id="autor_<?= $row['id'] ?>" name="autor" required /><br />
                            <label for="contenido_<?= $row['id'] ?>">Comentario:</label><br />
                            <textarea id="contenido_<?= $row['id'] ?>" name="contenido" rows="3" required></textarea><br />
                            <button type="submit">Agregar comentario</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function editRecord(id, nombre, ingredientes, instrucciones, tiempo, porciones, categoria) {
            document.getElementById('id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('ingredientes').value = ingredientes;
            document.getElementById('instrucciones').value = instrucciones;
            document.getElementById('tiempo_preparacion').value = tiempo;
            document.getElementById('porciones').value = porciones;
            document.getElementById('categoria').value = categoria;
        }
    </script>
</body>
</html>
