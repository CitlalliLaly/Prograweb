<?php
session_start();
include '../../Includes/conexion.php';

// Validaciones ANTES de include header
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

include '../../Includes/header.php';

$usuario_id = $_SESSION['id_usuario'];

// Obtener id_profesor desde la tabla usuarios
$stmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid");
$stmt->execute([':uid' => $usuario_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id_profesor = $row ? $row['id_profesor'] : null;

if (!$id_profesor) {
    echo "<div class='container mt-4'><div class='alert alert-warning'>No se encontró perfil de profesor asociado.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

// Obtener inscripciones pendientes a aceptar o no
$sql_pendientes = "SELECT ins.ID_inscripcion, ins.ID_alumno, ins.ID_curso, ins.estado, ins.fecha_solicitud,
               a.nombre AS nombre_alumno, a.apellido AS apellido_alumno, a.foto_perfil, c.nombre_curso, c.id_curso
        FROM inscripciones ins
        JOIN cursos c ON ins.ID_curso = c.id_curso
        JOIN alumnos a ON ins.ID_alumno = a.id_alumno
        WHERE c.id_profesor = :id_profesor AND ins.estado = 'Pendiente'
        ORDER BY ins.fecha_solicitud ASC";

$stmt_pend = $conexion->prepare($sql_pendientes);
$stmt_pend->execute([':id_profesor' => $id_profesor]);
$solicitudes_pendientes = $stmt_pend->fetchAll(PDO::FETCH_ASSOC);

// Obtener inscripciones aprobadas en el momento    
$sql_aprobadas = "SELECT ins.ID_inscripcion, ins.ID_alumno, ins.ID_curso, ins.estado, ins.fecha_solicitud,
               a.nombre AS nombre_alumno, a.apellido AS apellido_alumno, c.nombre_curso
        FROM inscripciones ins
        JOIN cursos c ON ins.ID_curso = c.id_curso
        JOIN alumnos a ON ins.ID_alumno = a.id_alumno
        WHERE c.id_profesor = :id_profesor AND ins.estado = 'Aprobado'
        ORDER BY ins.fecha_solicitud DESC
        LIMIT 20";

$stmt_apro = $conexion->prepare($sql_aprobadas);
$stmt_apro->execute([':id_profesor' => $id_profesor]);
$solicitudes_aprobadas = $stmt_apro->fetchAll(PDO::FETCH_ASSOC);

// Obtener inscripciones rechazadas los perdedores dice mi ama
$sql_rechazadas = "SELECT ins.ID_inscripcion, ins.ID_alumno, ins.ID_curso, ins.estado, ins.fecha_solicitud,
               a.nombre AS nombre_alumno, a.apellido AS apellido_alumno, c.nombre_curso
        FROM inscripciones ins
        JOIN cursos c ON ins.ID_curso = c.id_curso
        JOIN alumnos a ON ins.ID_alumno = a.id_alumno
        WHERE c.id_profesor = :id_profesor AND ins.estado = 'Rechazado'
        ORDER BY ins.fecha_solicitud DESC
        LIMIT 20";

$stmt_rech = $conexion->prepare($sql_rechazadas);
$stmt_rech->execute([':id_profesor' => $id_profesor]);
$solicitudes_rechazadas = $stmt_rech->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="contenedor-centrado-xl relleno-40-20">
    <h2 class="titulo-principal-grande"><i class="fas fa-user-check"></i> Solicitudes de Inscripción</h2>

    <?php if (isset($_SESSION['mensaje_ok'])): ?>
        <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($_SESSION['mensaje_ok']); unset($_SESSION['mensaje_ok']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($_SESSION['mensaje_error']); unset($_SESSION['mensaje_error']); ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-pendientes" data-bs-toggle="tab" data-bs-target="#content-pendientes" type="button" role="tab">
                <i class="fas fa-hourglass-half"></i> Pendientes <span class="badge bg-warning text-dark"><?php echo count($solicitudes_pendientes); ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-aprobadas" data-bs-toggle="tab" data-bs-target="#content-aprobadas" type="button" role="tab">
                <i class="fas fa-check-circle"></i> Aprobadas <span class="badge bg-success"><?php echo count($solicitudes_aprobadas); ?></span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-rechazadas" data-bs-toggle="tab" data-bs-target="#content-rechazadas" type="button" role="tab">
                <i class="fas fa-times-circle"></i> Rechazadas <span class="badge bg-danger"><?php echo count($solicitudes_rechazadas); ?></span>
            </button>
        </li>
    </ul>

    <!-- CONTENIDO DE TABS -->
    <div class="tab-content">
        <!-- PENDIENTES -->
        <div class="tab-pane fade show active" id="content-pendientes" role="tabpanel">
            <?php if (empty($solicitudes_pendientes)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay solicitudes pendientes.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($solicitudes_pendientes as $s): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-warning bg-opacity-10 border-bottom border-warning">
                                    <h6 class="mb-0"><i class="fas fa-exclamation"></i> Pendiente de revisión</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <?php if (!empty($s['foto_perfil'])): ?>
                                            <img src="<?php echo htmlspecialchars($s['foto_perfil']); ?>" alt="Foto" class="rounded-circle" style="width:50px;height:50px;object-fit:cover;border:2px solid #ddd;margin-right:12px;">
                                        <?php else: ?>
                                            <i class="fas fa-user-circle" style="font-size:50px;color:#999;margin-right:12px;"></i>
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($s['nombre_alumno'] . ' ' . $s['apellido_alumno']); ?></h6>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Curso:</strong> <?php echo htmlspecialchars($s['nombre_curso']); ?></p>
                                        <p class="mb-0"><strong>Fecha solicitud:</strong> <small><?php echo date('d/m/Y H:i', strtotime($s['fecha_solicitud'])); ?></small></p>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <form action="/Proyecto_integral/api/profesor/procesar_inscripcion.php" method="POST" class="flex-grow-1">
                                            <input type="hidden" name="id_inscripcion" value="<?php echo $s['ID_inscripcion']; ?>">
                                            <input type="hidden" name="accion" value="aprobar">
                                            <button class="btn btn-success w-100 btn-sm" type="submit"><i class="fas fa-check"></i> Aprobar</button>
                                        </form>
                                        <form action="/Proyecto_integral/api/profesor/procesar_inscripcion.php" method="POST" class="flex-grow-1">
                                            <input type="hidden" name="id_inscripcion" value="<?php echo $s['ID_inscripcion']; ?>">
                                            <input type="hidden" name="accion" value="rechazar">
                                            <button class="btn btn-danger w-100 btn-sm" type="submit"><i class="fas fa-times"></i> Rechazar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- APROBADAS -->
        <div class="tab-pane fade" id="content-aprobadas" role="tabpanel">
            <?php if (empty($solicitudes_aprobadas)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay inscripciones aprobadas.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($solicitudes_aprobadas as $s): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-success bg-opacity-10 border-bottom border-success">
                                    <h6 class="mb-0"><i class="fas fa-check"></i> Aprobado</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($s['nombre_alumno'] . ' ' . $s['apellido_alumno']); ?></h6>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Curso:</strong> <?php echo htmlspecialchars($s['nombre_curso']); ?></p>
                                        <p class="mb-0"><strong>Aprobado:</strong> <small><?php echo date('d/m/Y H:i', strtotime($s['fecha_solicitud'])); ?></small></p>
                                    </div>
                                    <div class="badge bg-success">Inscripción activa</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- RECHAZADAS -->
        <div class="tab-pane fade" id="content-rechazadas" role="tabpanel">
            <?php if (empty($solicitudes_rechazadas)): ?>
                <div class="alert alert-info"><i class="fas fa-info-circle"></i> No hay solicitudes rechazadas.</div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($solicitudes_rechazadas as $s): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger">
                                    <h6 class="mb-0"><i class="fas fa-times"></i> Rechazado</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <h6 class="fw-bold"><?php echo htmlspecialchars($s['nombre_alumno'] . ' ' . $s['apellido_alumno']); ?></h6>
                                    </div>
                                    <div class="mb-3">
                                        <p class="mb-1"><strong>Curso:</strong> <?php echo htmlspecialchars($s['nombre_curso']); ?></p>
                                        <p class="mb-0"><strong>Rechazado:</strong> <small><?php echo date('d/m/Y H:i', strtotime($s['fecha_solicitud'])); ?></small></p>
                                    </div>
                                    <div class="badge bg-danger">Inscripción rechazada</div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="margen-superior-30 texto-centrado-personalizado">
        <a href="/Proyecto_integral/views/profesor/index.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver al Panel</a>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
