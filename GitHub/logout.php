<?php
// Incluir la clase Auth
require_once 'Auth.php';

// Crear instancia de Auth sin conexión
$auth = new Auth();

// Ejecutar el cierre de sesión
$auth->logout();

// Redirigir al usuario a la página de login
header("Location: login.php");
exit;
