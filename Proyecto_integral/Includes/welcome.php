<?php
// Saludo centralizado 
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

$nombre = isset($_SESSION['nombre']) ? htmlspecialchars($_SESSION['nombre']) : '';
$rol = isset($_SESSION['rol']) ? strtolower($_SESSION['rol']) : '';

// Subtítulos por rol para mostrar debajo del título
switch ($rol) {
    case 'administrador':
        $sub = 'Gestiona el sistema desde aquí.';
        break;
    case 'profesor':
        $sub = 'Gestiona tus clases y actividades desde aquí.';
        break;
    case 'padre':
        $sub = 'Revisa el progreso y calificaciones de tus hijos.';
        break;
    case 'alumno':
        $sub = 'Resumen rápido de tu actividad escolar.';
        break;
    default:
        $sub = '';
}

if ($nombre) {
    echo '<div class="contenedor-flex-40">';
    echo '  <div class="contenedor-centrado-xl">';
    echo '    <h2 class="titulo-principal-medio">Bienvenido/a ' . $nombre . '</h2>';
    if ($sub !== '') {
        echo '    <p class="subtitulo-atenuado margen-inferior-30">' . htmlspecialchars($sub) . '</p>';
    }
    echo '  </div>';
    echo '</div>';
}
?>