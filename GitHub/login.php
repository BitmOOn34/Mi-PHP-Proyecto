<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['modo'] === 'invitado') {
        $_SESSION['rol'] = 'invitado';
        $_SESSION['usuario_id'] = null;
        $_SESSION['usuario_nombre'] = 'Invitado';  // El nombre predeterminado para el invitado
        header("Location: index.php");
        exit;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($usuario = $res->fetch_assoc()) {
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nombre'] = $usuario['nombre'];  // Almacena el nombre del usuario
            $_SESSION['rol'] = 'usuario';
            header("Location: index.php");
            exit;
        }
    }
    $error = "Correo o contraseña incorrectos.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="login-body">
    <div class="login-container">
        <h2>Iniciar sesión</h2>
        <?php if (isset($error)) echo "<p class='mensaje'>$error</p>"; ?>

        <form method="POST">
            <input type="email" name="email" placeholder="Correo" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit" name="modo" value="usuario">Entrar</button>
        </form>

        <form method="POST">
            <button type="submit" name="modo" value="invitado" class="btn-invitado">Entrar como invitado</button>
        </form>

        <p class="registro-link">¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</body>
</html>
