<?php
session_start();
include 'db.php';
include 'UserModel.php';

$userModel = new UserModel($conn);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    if (!$nombre || !$email || !$password) {
        $error = "Todos los campos son obligatorios.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } elseif ($userModel->existeEmail($email)) {
        $error = "El correo ya está registrado.";
    } else {
        try {
            // Registrar usuario y guardar datos de sesión
            $usuarioId = $userModel->registrarUsuario($nombre, $email, $password);
            $_SESSION['usuario_id'] = $usuarioId;
            $_SESSION['rol'] = 'usuario';
            $_SESSION['usuario_nombre'] = $nombre;
            header("Location: index.php");
            exit;
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Mostrar formulario HTML (vista)
include 'registro_form.php';
