<?php
session_start();

require_once 'db.php';
require_once 'Auth.php';

// Crear instancia de la clase Auth con la conexión a la BD
$auth = new Auth($conn);

$error = ''; // Variable para mensajes de error

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modo = $_POST['modo'] ?? '';

    // Login como invitado (sin usuario registrado)
    if ($modo === 'invitado') {
        $auth->loginInvitado();
        header("Location: index.php");
        exit;
    }

    // Recoger y sanear datos del formulario
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validaciones básicas
    if (!$email || !$password) {
        $error = "Por favor, completa todos los campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Correo electrónico no válido.";
    } elseif ($auth->login($email, $password)) {
        // Login exitoso, redirigir al index
        header("Location: index.php");
        exit;
    } else {
        // Credenciales incorrectas
        $error = "Correo o contraseña incorrectos.";
    }
}

// Incluir la vista del formulario de login 
include 'login_form.php';
