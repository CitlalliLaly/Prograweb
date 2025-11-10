<?php
session_start();

// Para debug - ver qué datos están llegando
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si se recibieron los datos
if(!isset($_POST['usuario']) || !isset($_POST['clave']) || !isset($_POST['color'])) {
    header("Location: index.php?error=No se recibieron todos los campos necesarios");
    exit();
}

$usuario = $_POST['usuario'];
$clave = $_POST['clave'];
$color = $_POST['color'];
$recordar = isset($_POST['recordar']) ? true : false;

// Datos fijos
$user_valido = "Profe";
$clave_valida = "12345";
$color_valido = "Rojo";

// Validar campos vacíos
if(empty($usuario) || empty($clave) || empty($color)){
    header("Location: index.php?error=Faltan campos por llenar");
    exit();
}

// Validar usuario y contraseña
if($usuario == $user_valido && $clave == $clave_valida){
    $_SESSION['usuario'] = $usuario;
    $_SESSION['color'] = $color;
    $_SESSION['autenticado'] = true;
    
    // Si se marcó la casilla de recordar
    if($recordar){
        // Crear cookie que dura 30 días
        setcookie('usuario', $usuario, time() + (30 * 24 * 60 * 60), '/');
        setcookie('color', $color, time() + (30 * 24 * 60 * 60), '/');
    }
    
    header("Location: menu.php");
    exit();
} else {
    header("Location: index.php?error=Usuario o contraseña incorrectos");
    exit();
}
?>