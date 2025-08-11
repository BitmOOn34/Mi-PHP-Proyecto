<?php
session_start();

if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'invitado';
}

if (!isset($_SESSION['usuario_id'])) {
    $_SESSION['usuario_id'] = null;
}

if (!isset($_SESSION['usuario_nombre'])) {
    $_SESSION['usuario_nombre'] = 'Invitado';
}
