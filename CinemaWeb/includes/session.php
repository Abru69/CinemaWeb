<?php
session_start();

function verificarRol($rol) {
    if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== $rol) {
        header('Location: ../login.php');
        exit();
    }
}

function iniciarSesion($usuario, $rol) {
    $_SESSION['usuario'] = $usuario;
    $_SESSION['rol'] = $rol;
}

function cerrarSesion() {
    session_unset();
    session_destroy();
    header('Location: ../login.php');
    exit();
}
?>