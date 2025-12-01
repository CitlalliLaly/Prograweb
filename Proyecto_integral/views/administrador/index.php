<?php
session_start();

// Verificar si es administrador PRIMERO
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

include '../../Includes/header.php';
include '../../Includes/welcome.php';
?>
<div class="contenedor-flex-40">
    <div class="contenedor-centrado-xl">
        <div class="row g-4 mb-5">
            
            <!-- Agrega el usuario -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/agregar_usuario.php" class="card border-0 shadow-sm text-decoration-none h-100 transicion-todos">
                    <div class="card-body text-center relleno-30">
                        <div class="color-icono-principal">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h5 class="card-title titulo-principal">Agregar Usuario</h5>
                        <p class="text-muted small">Crea un nuevo usuario en el sistema</p>
                    </div>
                </a>
            </div>

            <!-- Agrega materia -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/agregar_materia.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-brown">
                            <i class="fas fa-book-medical"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Agregar Materia</h5>
                        <p class="text-muted small">Crear una nueva asignatura</p>
                    </div>
                </a>
            </div>

            <!-- Gestión de Usuarios -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/listar_usuarios.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Gestión de Usuarios</h5>
                        <p class="text-muted small">Ver y editar usuarios del sistema</p>
                        <div class="mt-2"><span class="badge bg-warning text-dark">Solo administradores</span></div>
                    </div>
                </a>
            </div>

            <!-- Resetear Contraseñas -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/resetear_usuarios.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-orange">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Resetear Contraseñas</h5>
                        <p class="text-muted small">Resetea contraseñas de usuarios</p>
                    </div>
                </a>
            </div>

            <!-- Gestión de Profesores y Materias -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/tools/revisar_profesores.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-green">
                            <i class="fas fa-chalkboard-user"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Profesores y Materias</h5>
                        <p class="text-muted small">Asigna materias a profesores</p>
                    </div>
                </a>
            </div>

            <!-- CRUD de Materias -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/materias_list.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-purple">
                            <i class="fas fa-book"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">CRUD de Materias</h5>
                        <p class="text-muted small">Ver, crear, editar y eliminar materias</p>
                    </div>
                </a>
            </div>

            <!-- Reportes -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/reportes_materias.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-red">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Reportes del Sistema</h5>
                        <p class="text-muted small">Informes: materias, asignaciones y estadísticas</p>
                    </div>
                </a>
            </div>

            <!-- Departamentos -->
            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/views/administrador/departamentos_list.php" class="card border-0 shadow-sm text-decoration-none h-100 card-transition">
                    <div class="card-body text-center card-body-padding-30">
                        <div class="big-icon icon-grey">
                            <i class="fas fa-building"></i>
                        </div>
                        <h5 class="card-title primary-dark-strong">Departamentos</h5>
                        <p class="text-muted small">Gestionar departamentos académicos</p>
                    </div>
                </a>
            </div>

        </div>

    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
