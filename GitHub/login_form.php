<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="styles.css" />
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Iniciar sesión</h2>

        <?php if (!empty($error)): ?>
            <p class="mensaje" style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" novalidate>
            <input
                type="email"
                name="email"
                placeholder="Correo"
                required
                autocomplete="username"
                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
            />
            <input
                type="password"
                name="password"
                placeholder="Contraseña"
                required
                autocomplete="current-password"
            />
            <button type="submit" name="modo" value="usuario">Entrar</button>
        </form>

        <form method="POST" style="margin-top: 1em;">
            <button type="submit" name="modo" value="invitado" class="btn-invitado">Entrar como invitado</button>
        </form>

        <p class="registro-link">
            ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
        </p>
    </div>
</body>
</html>
