<?php
session_start();
$page_title = "Inicio | CUSJ";

// Si está logueado, redirigir a su dashboard ANTES de incluir header
if (isset($_SESSION['id_usuario'])) {
    $rol = strtolower($_SESSION['rol']);
    switch ($rol) {
        case 'administrador':
            header('Location: views/administrador/index.php');
            break;
        case 'profesor':
        case 'maestro':
            header('Location: views/profesor/index.php');
            break;
        case 'alumno':
        case 'estudiante':
            header('Location: home/usuarios/index_alumno.php');
            break;
        case 'padre':
            header('Location: views/padre/index.php');
            break;
        default:
            // Sin rol definido, continuas en index por jugarle al chido
            break;
    }
    exit();
}

include 'Includes/header.php';
?>

<div class="contenedor centrado">
    <div class="caja-bienvenida">
        <img src="<?php echo $base_path; ?>assets/imgs/logo.png" alt="Logo CUSJ" class="logo-central" id="logo-central">

        <h1>Bienvenido al CUSJ</h1>
        <h2>Plataforma de Control Escolar</h2>
        
        <p>
            Este es el sistema para gestionar tus calificaciones y materias.
            <br>
            Presiona <b>Ingresar</b> o <b>Registrarte</b> según lo que requieras realizar.
        </p>

        <nav class="nav-buttons">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <a href="<?php echo $base_path; ?>logout.php" class="nav-btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                </a>
            <?php else: ?>
                <a href="<?php echo $base_path; ?>login.php" class="nav-btn btn-login">
                    <i class="fas fa-sign-in-alt"></i> Ingresar
                </a>
                <a href="<?php echo $base_path; ?>registro.php" class="nav-btn btn-register">
                    Registrarse
                </a>
            <?php endif; ?>
        </nav>

    </div>
</div>

<?php include 'Includes/footer.php'; ?>