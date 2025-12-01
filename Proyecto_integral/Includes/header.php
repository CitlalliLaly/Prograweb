<?php
// Asegurar que la sesión esté iniciada antes de usar $_SESSION
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Asegurar que exista la conexión a la base de datos disponible para el header.
// Muchos scripts incluyen `Includes/header.php` sin incluir `conexion.php` antes,
// así que incluimos la conexión aquí si no está ya definida.
if (!isset($conexion)) {
    $possible = __DIR__ . '/conexion.php';
    if (file_exists($possible)) {
        include $possible;
    }
}

// Usar URL base absoluta del proyecto para que los enlaces funcionen desde cualquier ruta ya que estuve una semana con ese error
$base_url = '/Proyecto_integral';
$base_path = rtrim($base_url, '/') . '/';

// Si estamos en una página de materia, capturar id para enlaces rápidos
$cur_materia_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

// Determinar si mostrar la barra de búsqueda automáticamente.
// Por defecto la mostramos en todas las páginas salvo cuando es login, index principal o registro
$script_name = basename($_SERVER['SCRIPT_NAME']);
$excluded_for_search = ['index.php', 'registro.php', 'login.php'];
// Si alguna plantilla ya setea $show_search, respeta esa configuración
if (!isset($show_search)) {
    $show_search = !in_array($script_name, $excluded_for_search);
}

// Foto de perfil e las isgnias recientes para el usuario logueado
$foto_perfil = null;
$insignias_count = 0;
$insignias_recientes = [];
if (isset($_SESSION['id_usuario']) && isset($_SESSION['rol'])) {
    $rol = strtolower($_SESSION['rol']);
    try {
        // Determinar id de usuario para las tablas de alumno (algunas tablas usan id_alumno)
        $badge_user_id = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];

        if ($rol === 'alumno') {
            // Foto perfil alumno
            try {
                $sql = "SELECT foto_perfil FROM alumnos WHERE id_alumno = :id LIMIT 1";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $badge_user_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $foto_perfil = $result['foto_perfil'] ?? null;
            } catch (Exception $e) {
               
            }

            // Obtener conteo y recientes de insignias (si la tabla existe)
            try {
                $sql_badges = "SELECT COUNT(*) FROM user_achievements WHERE id_usuario = :uid";
                $stmt_badges = $conexion->prepare($sql_badges);
                $stmt_badges->execute([':uid' => $badge_user_id]);
                $insignias_count = (int)$stmt_badges->fetchColumn();

                $sql_recent = "SELECT a.titulo, a.icono, ua.otorgado_en FROM user_achievements ua JOIN achievements a ON a.id = ua.id_achievement WHERE ua.id_usuario = :uid ORDER BY ua.otorgado_en DESC LIMIT 5";
                $stmt_recent = $conexion->prepare($sql_recent);
                $stmt_recent->execute([':uid' => $badge_user_id]);
                $insignias_recientes = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $insignias_count = 0;
                $insignias_recientes = [];
            }

            // Obtener una materia por defecto para enlaces rápidos
            $default_materia_id = null;
            try {
                $qmat = $conexion->prepare("SELECT c.id_materia FROM inscripciones i JOIN cursos c ON i.ID_curso = c.id_curso WHERE i.ID_alumno = :uid AND i.estado = 'Aprobado' LIMIT 1");
                $qmat->execute([':uid' => $badge_user_id]);
                $mrow = $qmat->fetch(PDO::FETCH_ASSOC);
                if ($mrow && !empty($mrow['id_materia'])) {
                    $default_materia_id = (int)$mrow['id_materia'];
                }
            } catch (Exception $e) {
                $default_materia_id = null;
            }

        } elseif (in_array($rol, ['profesor', 'maestro'])) {
            try {
                $sql = "SELECT foto_perfil FROM profesores WHERE id_profesor = :id LIMIT 1";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $_SESSION['id_profesor']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $foto_perfil = $result['foto_perfil'] ?? null;
            } catch (Exception $e) {
            }
        } elseif ($rol === 'administrador') {
            try {
                $sql = "SELECT foto_perfil FROM administradores WHERE id_admin = :id LIMIT 1";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':id' => $_SESSION['id_admin']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $foto_perfil = $result['foto_perfil'] ?? null;
            } catch (Exception $e) {
            }
        }
    } catch (Exception $e) {
        // Si hay error, se deja el  valor por defecto
    }
}
// Construimos las insignias recientes 
$insignias_tooltip = '';
if (!empty($insignias_recientes)) {
    $parts = [];
    foreach ($insignias_recientes as $it) {
        $parts[] = $it['titulo'];
    }
    $insignias_tooltip = implode(', ', $parts);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'CUSJ | Control Escolar'; ?></title>
    <link rel="icon" href="<?php echo $base_url; ?>/assets/imgs/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo $base_url; ?>/assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
</head>
<body>

    <header class="main-navbar">
        <div class="contenedor-header d-flex align-items-center">
            <img src="<?php echo $base_url; ?>/assets/imgs/logo.png" alt="Logo CUSJ" class="nav-logo">
            <span class="ms-2 logo-escolar logo-escolar-strong">Escolar</span>
        </div>

        <?php if (isset($_SESSION['id_usuario'])): ?>
        <div class="search-box mx-3 search-box--wide">
            <form action="<?php echo $base_url; ?>/buscar.php" method="GET">
                <div class="input-group">
                    <input type="search" name="q" class="form-control rounded-start-pill search-input" placeholder="Buscar cursos, actividades o alumnos..." aria-label="Buscar" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
                    <button class="btn btn-light rounded-end-pill" type="submit" aria-label="Buscar">
                        <i class="fas fa-search text-muted"></i>
                    </button>
                </div>
            </form>
        </div>
        <?php endif; ?>

        <div class="user-menu">
            <?php if (isset($_SESSION['id_usuario'])): ?>
                <?php if (isset($_SESSION['rol'])): ?>
                    <?php $r = strtolower($_SESSION['rol']); ?>
                    <?php if ($r === 'alumno'): ?>
                        <a href="<?php echo $base_url; ?>/views/alumno/perfil.php" class="user-name mx-3 enlace-usuario-perfil">
                            <?php if (!empty($foto_perfil)): ?>
                                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto" class="imagen-usuario-header">
                            <?php else: ?>
                                <i class="fas fa-user-circle icono-usuario-circulo"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                        </a>
                        <div class="badge-insignia ms-2" title="<?php echo htmlspecialchars($insignias_tooltip); ?>">
                            <a href="<?php echo $base_url; ?>/views/alumno/perfil.php#insignias" class="no-decoration-inherit">
                                <i class="fas fa-star"></i>
                                <span class="ms-1"><?php echo (int)$insignias_count; ?></span>
                            </a>
                        </div>
                    <?php elseif (in_array($r, ['profesor','maestro'])): ?>
                        <a href="<?php echo $base_url; ?>/views/profesor/perfil.php" class="user-name mx-3 enlace-usuario-perfil">
                            <?php if (!empty($foto_perfil)): ?>
                                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto" class="imagen-usuario-header">
                            <?php else: ?>
                                <i class="fas fa-user-circle icono-usuario-circulo"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                        </a>
                    <?php elseif ($r === 'administrador'): ?>
                        <a href="<?php echo $base_url; ?>/views/administrador/perfil.php" class="user-name mx-3 enlace-usuario-perfil">
                            <?php if (!empty($foto_perfil)): ?>
                                <img src="<?php echo htmlspecialchars($foto_perfil); ?>" alt="Foto" class="imagen-usuario-header">
                            <?php else: ?>
                                <i class="fas fa-user-shield icono-usuario-escudo"></i>
                            <?php endif; ?>
                            <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
                <a href="<?php echo $base_url; ?>/logout.php" class="btn btn-danger btn-sm" title="Cerrar sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php endif; ?>
        </div>
    </header>

    <!-- NAVBAR DE NAVEGACIÓN: Enlaces (#C1FF72) con menús personalizados -->
    <nav class="navbar navbar-c1" role="navigation" aria-label="navigation">
        <div class="navbar-brand">
            <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarMenuC1">
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
                <span aria-hidden="true"></span>
            </a>
        </div>

        <div id="navbarMenuC1" class="navbar-menu">
            <div class="navbar-start">
                <?php if (isset($_SESSION['id_usuario'])): ?>
                    <?php $r = strtolower($_SESSION['rol']); ?>

                    <!-- MENÚ PARA TODOS -->
                    <a class="navbar-item" href="<?php echo $base_url; ?>/index.php">
                        <i class="fas fa-home"></i> <span class="ms-2">Inicio</span>
                    </a>

                    <!-- MENÚ PARA ALUMNO -->
                    <?php if ($r === 'alumno'): ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-book"></i> <span class="ms-2">Mis Materias</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/usuarios/index_alumno.php">
                                    <i class="fas fa-list"></i> Ver todas
                                </a>
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/usuarios/inscribir.php">
                                    <i class="fas fa-plus-circle"></i> Inscribirse
                                </a>
                            </div>
                        </div>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-tasks"></i> <span class="ms-2">Tareas</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?php echo ($cur_materia_id ?? $default_materia_id) ? $base_url . '/views/alumno/ver_materia.php?id=' . ($cur_materia_id ?? $default_materia_id) : $base_url . '/home/usuarios/index_alumno.php'; ?>">
                                    <i class="fas fa-hourglass-half"></i> Próximas entregas
                                </a>
                                <a class="navbar-item" href="<?php echo ($cur_materia_id ?? $default_materia_id) ? $base_url . '/views/alumno/ver_materia.php?id=' . ($cur_materia_id ?? $default_materia_id) : $base_url . '/home/usuarios/index_alumno.php'; ?>">
                                    <i class="fas fa-file-upload"></i> Mis entregas
                                </a>
                            </div>
                        </div>

                    <!-- MENÚ PARA PROFESOR -->
                    <?php elseif (in_array($r, ['profesor', 'maestro'])): ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-book"></i> <span class="ms-2">Mis Materias</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?php echo $base_url; ?>/views/profesor/index.php">
                                    <i class="fas fa-list"></i> Ver todas
                                </a>
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/profesor/alumnos.php">
                                    <i class="fas fa-users"></i> Mis alumnos
                                </a>
                            </div>
                        </div>

                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-tasks"></i> <span class="ms-2">Actividades</span>
                            </a>
                            <div class="navbar-dropdown">
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/profesor/actividades.php">
                                    <i class="fas fa-list"></i> Ver actividades
                                </a>
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/profesor/crear_actividad.php">
                                    <i class="fas fa-plus-circle"></i> Crear nueva
                                </a>
                                <a class="navbar-item" href="<?php echo $base_url; ?>/home/profesor/calificar.php">
                                    <i class="fas fa-pen-fancy"></i> Calificar entregas
                                </a>
                            </div>
                        </div>

                        <a class="navbar-item" href="<?php echo $base_url; ?>/home/profesor/inscripciones.php">
                            <i class="fas fa-user-check"></i> <span class="ms-2">Solicitudes</span>
                        </a>

                    <!-- MENÚ PARA ADMINISTRADOR -->
                    <?php elseif ($r === 'administrador'): ?>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-book"></i> <span class="ms-2">Materias</span>
                            </a>
                            <div class="navbar-dropdown">
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/materias_list.php">
                                    <i class="fas fa-list"></i> Listar
                                </a>
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/agregar_materia.php">
                                    <i class="fas fa-plus-circle"></i> Agregar
                                </a>
                            </div>
                        </div>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-users"></i> <span class="ms-2">Usuarios</span>
                            </a>
                            <div class="navbar-dropdown">
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/listar_usuarios.php">
                                    <i class="fas fa-list"></i> Listar
                                </a>
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/agregar_usuario.php">
                                    <i class="fas fa-user-plus"></i> Agregar
                                </a>
                            </div>
                        </div>
                        <div class="navbar-item has-dropdown is-hoverable">
                            <a class="navbar-link">
                                <i class="fas fa-building"></i> <span class="ms-2">Departamentos</span>
                            </a>
                            <div class="navbar-dropdown">
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/departamentos_list.php">
                                    <i class="fas fa-list"></i> Listar
                                </a>
                                    <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/agregar_departamento.php">
                                    <i class="fas fa-plus-circle"></i> Agregar
                                </a>
                            </div>
                        </div>

                            <a class="navbar-item" href="<?php echo $base_url; ?>/views/administrador/ranking_alumnos.php">
                            <i class="fas fa-trophy"></i> <span class="ms-2">Ranking</span>
                        </a>

                    <!-- MENÚ PARA PADRE -->
                    <?php elseif ($r === 'padre'): ?>
                        <a class="navbar-item" href="<?php echo $base_url; ?>/views/padre/index.php">
                            <i class="fas fa-child"></i> <span class="ms-2">Mis hijos</span>
                        </a>

                        <a class="navbar-item" href="<?php echo $base_url; ?>/views/padre/perfil.php">
                            <i class="fas fa-chart-line"></i> <span class="ms-2">Calificaciones</span>
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <script>
    
    document.addEventListener('DOMContentLoaded', function () {
        var burgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);
        if (burgers.length > 0) {
            burgers.forEach(function (el) {
                el.addEventListener('click', function () {
                    var target = el.dataset.target;
                    var $target = document.getElementById(target);
                    el.classList.toggle('is-active');
                    if ($target) $target.classList.toggle('is-active');
                });
            });
        }
    });
    </script>
    