<?php
session_start();
if (!isset($_SESSION['rol'])) {
    $_SESSION['rol'] = 'invitado';
}
