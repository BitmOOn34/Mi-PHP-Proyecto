<?php
session_start();
include 'db.php';

$rol = $_SESSION['rol'] ?? 'invitado';
$usuario_id = $_SESSION['usuario_id'] ?? null;

$filtro = $_GET['busqueda'] ?? '';
$filtro_sql = "%$filtro%";

$stmt = $conn->prepare("SELECT id, nombre, ingredientes, instrucciones, tiempo_preparacion, porciones, categoria, imagen, calificacion, usuario_id FROM recetas WHERE nombre LIKE ? OR ingredientes LIKE ?");
$stmt->bind_param("ss", $filtro_sql, $filtro_sql);
$stmt->execute();
$result = $stmt->get_result();
?>
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

        <div style="text-align: right; margin-bottom: 10px;">
            <?php if ($rol !== 'invitado'): ?>
                <strong>Bienvenido</strong> | <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión / Registrarse</a>
            <?php endif; ?>
        </div>

        <div class="imagen-portada">
            <img src="fondos/Recetas.avif" alt="Imagen de presentación de recetas" />
        </div>

        <form method="GET" action="">
            <input type="text" name="busqueda" placeholder="Buscar por nombre o ingrediente..." value="<?= htmlspecialchars($filtro) ?>" />
            <button type="submit">Buscar</button>
        </form>

        <?php if ($rol !== 'invitado'): ?>
        <form action="process.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" id="id" />
            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>" />

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
        <?php else: ?>
            <p style="color: red;">Inicia sesión para poder agregar o editar recetas.</p>
        <?php endif; ?>

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
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td>
                        <?php if (!empty($row['imagen']) && file_exists("uploads/" . $row['imagen'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['imagen']) ?>" alt="<?= htmlspecialchars($row['nombre']) ?>" width="100" />
                        <?php else: ?>
                            <em>Sin imagen</em>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['categoria']) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['ingredientes'])) ?></td>
                    <td><?= nl2br(htmlspecialchars($row['instrucciones'])) ?></td>
                    <td><?= htmlspecialchars($row['tiempo_preparacion']) ?></td>
                    <td><?= htmlspecialchars($row['porciones']) ?></td>
                    <td>
                        <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 6px;">
                            <span><?= number_format($row['calificacion'], 1) ?> / 5</span>
                            <?php if ($rol !== 'invitado'): ?>
                            <form action="calificar.php" method="POST" style="display: flex; gap: 4px;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>" />
                                <select name="calificacion" onchange="this.form.submit()" style="padding: 4px; border-radius: 6px;">
                                    <option value="">Calificar</option>
                                    <?php for ($i=1; $i<=5; $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> ⭐</option>
                                    <?php endfor; ?>
                                </select>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>
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
                <tr>
                    <td colspan="9" style="background:#fafafa; text-align:left;">
                        <strong>Comentarios:</strong><br />
                        <?php
                        $idReceta = $row['id'];
                        $comentariosSql = $conn->prepare("SELECT * FROM comentarios WHERE receta_id = ? ORDER BY fecha DESC");
                        $comentariosSql->bind_param("i", $idReceta);
                        $comentariosSql->execute();
                        $comentariosResult = $comentariosSql->get_result();

                        if ($comentariosResult->num_rows > 0) {
                            while ($comentario = $comentariosResult->fetch_assoc()) {
                                echo "<p><b>" . htmlspecialchars($comentario['autor']) . "</b> (" . htmlspecialchars($comentario['fecha']) . "):<br>" .
                                     nl2br(htmlspecialchars($comentario['contenido'])) . "</p><hr>";
                            }
                        } else {
                            echo "<p>No hay comentarios aún.</p>";
                        }
                        ?>
                        <?php if ($rol !== 'invitado'): ?>
                        <form action="comentarios.php" method="POST">
                            <input type="hidden" name="receta_id" value="<?= $row['id'] ?>" />
                            <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($usuario_id) ?>" />
                            <label for="autor_<?= $row['id'] ?>">Nombre:</label><br />
                            <input type="text" id="autor_<?= $row['id'] ?>" name="autor" required /><br />
                            <label for="contenido_<?= $row['id'] ?>">Comentario:</label><br />
                            <textarea id="contenido_<?= $row['id'] ?>" name="contenido" rows="3" required></textarea><br />
                            <button type="submit">Agregar comentario</button>
                        </form>
                        <?php else: ?>
                            <p><em>Inicia sesión para comentar.</em></p>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="9" style="color: red; text-align: center; font-weight: bold;">No se encontró ninguna receta que coincida con tu búsqueda.</td>
                </tr>
                <?php endif; ?>
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
<?php
$stmt->close();
$conn->close();
?>
