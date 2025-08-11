<?php
session_start();

require_once 'db.php';
require_once 'RecetaModel.php';
require_once 'ComentarioModel.php';
require_once 'views.php';

// Rol del usuario actual (por defecto: invitado)
$rol = $_SESSION['rol'] ?? 'invitado';
$usuario_id = $_SESSION['usuario_id'] ?? null;

// Filtro de búsqueda (si se usó el campo de búsqueda)
$filtro = $_GET['busqueda'] ?? '';

// Instancias de modelos
$model = new RecetaModel($conn);
$recetas = $model->buscarRecetas($filtro);

$comentarioModel = new ComentarioModel($conn);
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

        <!-- Barra de sesión -->
        <div class="session-bar">
            <?php if ($rol !== 'invitado'): ?>
                <strong>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre'] ?? 'Usuario') ?></strong> |
                <a href="logout.php">Cerrar sesión</a>
            <?php else: ?>
                <a href="login.php">Iniciar sesión / Registrarse</a>
            <?php endif; ?>
        </div>

        <!-- Imagen de portada -->
        <div class="imagen-portada">
            <img src="fondos/Recetas.avif" alt="Imagen de presentación de recetas" />
        </div>

        <!-- Formulario de búsqueda -->
        <form method="GET" action="index.php" class="search-form">
            <input
                type="text"
                name="busqueda"
                placeholder="Buscar por nombre o ingrediente..."
                value="<?= htmlspecialchars($filtro) ?>"
            />
            <button type="submit">Buscar</button>
        </form>

        <!-- Formulario para agregar o editar recetas -->
        <?php include 'form_receta.php'; ?>

        <!-- Tabla con las recetas existentes -->
        <?php include 'tabla_recetas.php'; ?>
    </div>

    <!-- Script para rellenar el formulario de edición -->
    <script>
        function editRecord(id, nombre, ingredientes, instrucciones, tiempo, porciones, categoria) {
            document.getElementById('id').value = id;
            document.getElementById('nombre').value = nombre;
            document.getElementById('ingredientes').value = ingredientes;
            document.getElementById('instrucciones').value = instrucciones;
            document.getElementById('tiempo_preparacion').value = tiempo;
            document.getElementById('porciones').value = porciones;
            document.getElementById('categoria').value = categoria;

            // Opcional: hacer scroll automático al formulario
            document.getElementById('nombre').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>
