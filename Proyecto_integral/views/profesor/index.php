<?php
session_start();

// Verificar que sea profesor
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

include '../../Includes/header.php';
include '../../Includes/welcome.php';
?>
<div class="contenedor-flex-40">
    <div class="contenedor-centrado-xl">

        <div class="row g-4 mb-5">

            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/home/profesor/inscripciones.php" class="card border-0 shadow-sm text-decoration-none h-100 transicion-todos">
                    <div class="card-body text-center relleno-30">
                        <div class="color-icono-principal">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <h5 class="card-title titulo-principal">Inscripciones</h5>
                        <p class="text-muted small">Ver y gestionar inscripciones de alumnos</p>
                    </div>
                </a>
            </div>

            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/home/profesor/actividades.php" class="card border-0 shadow-sm text-decoration-none h-100 transicion-todos">
                    <div class="card-body text-center relleno-30">
                        <div class="color-icono-principal">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <h5 class="card-title titulo-principal">Actividades</h5>
                        <p class="text-muted small">Crear y ver todas las actividades de tus cursos</p>
                    </div>
                </a>
            </div>

            <!-- 'Calificar' removed from dashboard menu per request -->

            <div class="col-md-6 col-lg-4">
                <a href="<?php echo $base_url; ?>/home/profesor/alumnos.php" class="card border-0 shadow-sm text-decoration-none h-100 transicion-todos">
                    <div class="card-body text-center relleno-30">
                        <div class="color-icono-principal">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h5 class="card-title titulo-principal">Alumnos</h5>
                        <p class="text-muted small">Ver alumnos por curso y sus estados</p>
                    </div>
                </a>
            </div>

        </div>

    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
