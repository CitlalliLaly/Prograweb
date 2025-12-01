<?php
session_start();
// Validar que sea alumno 
if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) != 'alumno') {
    header("Location:index.php");
    exit();
}

include '../../Includes/conexion.php';

// Calcular ruta base web relativa
$base_url = '/Proyecto_integral';

include '../../Includes/header.php';

// ID del alumno desde sesión 
$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];

// NOTIFICACIONES
try {
    $qNot = $conexion->prepare("SELECT * FROM notificaciones WHERE id_usuario = :uid ORDER BY creado_en DESC LIMIT 50");
    $qNot->execute([':uid' => $id_alumno]);
    $notificaciones = $qNot->fetchAll(PDO::FETCH_ASSOC);

    $qUnread = $conexion->prepare("SELECT COUNT(*) FROM notificaciones WHERE id_usuario = :uid AND (leido IS NULL OR leido = 0)");
    $qUnread->execute([':uid' => $id_alumno]);
    $unread_count = (int)$qUnread->fetchColumn();
} catch (Exception $e) {
    $notificaciones = [];
    $unread_count = 0;
}

// la relación en la BD entre alumno y materia es a través de cursos e inscripciones
$sql_materias = "SELECT m.id_materia, m.nombre, m.descripcion
                 FROM cursos c
                 JOIN materia m ON c.id_materia = m.id_materia
                 JOIN inscripciones i ON c.id_curso = i.ID_curso
                 WHERE i.ID_alumno = :id_alumno AND i.estado = 'Aprobado'";
$stmt = $conexion->prepare($sql_materias);
$stmt->execute([':id_alumno' => $id_alumno]);
$mis_materias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tareas pendientes
$sql_tareas = "SELECT a.ID_actividad AS id, a.titulo, a.fecha_limite, m.nombre as materia, a.ID_curso
               FROM actividades a
               JOIN cursos c ON a.ID_curso = c.id_curso
               JOIN materia m ON c.id_materia = m.id_materia
               JOIN inscripciones i ON c.id_curso = i.ID_curso
               WHERE i.ID_alumno = :id_alumno
               AND i.estado = 'Aprobado'
               AND a.fecha_limite >= CURDATE()"
              ;

// Si existe la tabla 'entregas', filtramos actividades ya entregadas para no entregar algo duplicado
$check = $conexion->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'entregas'");
$check->execute();
$has_entregas = $check->fetchColumn() > 0;

if ($has_entregas) {
    $sql_tareas .= " AND a.ID_actividad NOT IN (SELECT id_actividad FROM entregas WHERE id_alumno = :id_alumno_ent)
                    ORDER BY a.fecha_limite ASC LIMIT 5";
    $stmt2 = $conexion->prepare($sql_tareas);
    $stmt2->execute([':id_alumno' => $id_alumno, ':id_alumno_ent' => $id_alumno]);
} else {
    $sql_tareas .= " ORDER BY a.fecha_limite ASC LIMIT 5";
    $stmt2 = $conexion->prepare($sql_tareas);
    $stmt2->execute([':id_alumno' => $id_alumno]);
}
$tareas_pendientes = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Calcular promedio por materia (esto es para que no te salga 0,NO LO BORRES TE CONOZCO CITLALLI DEL FUTURO)
foreach ($mis_materias as &$mat) {
    $mat['promedio'] = 'Sin calificaciones';
    $mat['estado'] = 'Pendiente';
    try {
        $sql_prom = "SELECT a.titulo, cal.calificacion_obtenida, a.ponderacion
                     FROM inscripciones i
                     JOIN cursos c ON i.ID_curso = c.id_curso
                     LEFT JOIN calificaciones cal ON i.ID_inscripcion = cal.ID_inscripcion
                     LEFT JOIN actividades a ON cal.ID_actividad = a.ID_actividad
                     WHERE i.ID_alumno = :id_alumno AND c.id_materia = :id_materia";
        $pstmt = $conexion->prepare($sql_prom);
        $pstmt->execute([':id_alumno' => $id_alumno, ':id_materia' => $mat['id_materia']]);
        $rows = $pstmt->fetchAll(PDO::FETCH_ASSOC);
        $actividades = [];
        foreach ($rows as $r) {
            if (is_numeric($r['calificacion_obtenida'])) {
                $actividades[] = ['calificacion' => $r['calificacion_obtenida'], 'ponderacion' => $r['ponderacion']];
            }
        }
        if (!empty($actividades)) {
            $prom = 0.0;
            $sumaP = 0.0;
            foreach ($actividades as $a) {
                $cal = is_numeric($a['calificacion']) ? (float)$a['calificacion'] : 0.0;
                $pond = is_numeric($a['ponderacion']) ? (float)$a['ponderacion'] : 0.0;
                $prom += $cal * $pond / 100;
                $sumaP += $pond;
            }
            if ($sumaP > 0) {
                $mat['promedio'] = round($prom, 2);
                $mat['estado'] = ($mat['promedio'] >= 60.0) ? 'Aprobado' : 'Reprobado';
            }
        }
    } catch (Exception $e) {
        // Silenciar errores y dejar valores por defecto esto nos ayuda a no romper la página si hay un error
    }
}
unset($mat);
?>

<?php include '../../Includes/welcome.php'; ?>

<div class="student-dashboard">

    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon stat-icon-bg-primary">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo count($mis_materias); ?></h4>
                <p>Materias Activas</p>
                <a class="small-link mt-2 d-block" href="<?php echo $base_url; ?>/views/alumno/ver_materia.php">Ver clases</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-bg-accent">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo count($tareas_pendientes); ?></h4>
                <p>Tareas Pendientes</p>
                <a class="small-link mt-2 d-block" href="<?php echo $base_url; ?>/home/usuarios/index_alumno.php">Mis tareas</a>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon stat-icon-bg-orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-content">
                <h4>9.8</h4>
                <p>Promedio General</p>
                <a class="small-link mt-2 d-block" href="<?php echo $base_url; ?>/views/alumno/perfil.php">Ver calificaciones</a>
            </div>
        </div>

        <div class="stat-card" role="button" data-bs-toggle="modal" data-bs-target="#modalNotificaciones" id="btn-notificaciones">
            <div class="stat-icon stat-icon-bg-blue">
                <i class="fas fa-bell"></i>
            </div>
            <div class="stat-content">
                <h4><?php echo isset($unread_count) ? $unread_count : 0; ?></h4>
                <p>Notificaciones</p>
            </div>
        </div>
    </div>

    <div class="dashboard-container">
        <!--  Materias Activas EL CHIDOOO -->
        <section class="materials-section">
            <h2><i class="fas fa-book"></i> Mis Materias Activas</h2>
            
            <?php if (!empty($mis_materias)): ?>
                <div class="materials-grid">
                    <?php foreach($mis_materias as $materia): ?>
                        <div class="material-card">
                            <div class="card-header">
                                <?php echo $materia['nombre']; ?>
                            </div>
                            <div class="card-content">
                                <p><?php echo substr($materia['descripcion'], 0, 80) . '...'; ?></p>
                                <div class="materia-meta">
                                    <a href="<?php echo $base_url; ?>/views/alumno/ver_materia.php?id=<?php echo $materia['id_materia']; ?>" class="btn-view btn-view-student">
                                        <i class="fas fa-chalkboard-teacher"></i>&nbsp; Ver clase y tareas
                                    </a>
                                    <div class="materia-badge materia-promedio">Prom: <?php echo is_numeric($materia['promedio']) ? $materia['promedio'] : $materia['promedio']; ?></div>
                                    <div class="materia-badge">Estado: <?php echo htmlspecialchars($materia['estado']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <p>No tienes materias inscritas o aceptadas aún.</p>
                    <a href="<?php echo $base_url; ?>/home/usuarios/inscribir.php" class="btn-primary">Inscribir ahora</a>
                </div>
            <?php endif; ?>
        </section>

        <!-- Tareas Pendientes -->
        <section class="tasks-section">
            <h2><i class="fas fa-clock"></i> Próximas Tareas</h2>
            
            <?php if (!empty($tareas_pendientes)): ?>
                <ul class="tasks-list">
                    <?php foreach($tareas_pendientes as $tarea): ?>
                        <li class="task-item">
                            <div class="task-info">
                                <h4><?php echo $tarea['titulo']; ?></h4>
                                <p><?php echo $tarea['materia']; ?></p>
                            </div>
                            <div class="task-date">
                                <?php 
                                $fecha = date_create($tarea['fecha_limite']);
                                echo date_format($fecha, 'd/M');
                                ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-check-circle"></i>
                    <p>¡Todo al día! No tienes tareas pendientes.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</div>
</div>

<!--Notificaciones -->
<div class="modal fade" id="modalNotificaciones" tabindex="-1" aria-labelledby="modalNotificacionesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
        <h5 class="modal-title" id="modalNotificacionesLabel">Notificaciones</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
        <?php if (!empty($notificaciones)): ?>
            <h6>Recientes</h6>
            <ul class="list-group mb-3">
                <?php foreach ($notificaciones as $n): ?>
                    <li class="list-group-item<?php echo (isset($n['leido']) && $n['leido']) ? '' : ' unread'; ?> d-flex justify-content-between align-items-start">
                        <div>
                            <strong><?php echo htmlspecialchars($n['titulo']); ?></strong>
                            <div class="text-muted small"><?php echo htmlspecialchars($n['tipo']); ?> · <?php echo date('d M Y H:i', strtotime($n['creado_en'] ?? 'now')); ?></div>
                            <div class="mt-1"><?php echo nl2br(htmlspecialchars($n['mensaje'])); ?></div>
                        </div>
                        <div class="text-end">
                            <?php if (!empty($n['referencia_tipo']) && !empty($n['referencia_id'])): ?>
                                <a href="<?php echo $base_url; ?>/views/alumno/perfil.php" class="btn btn-sm btn-outline-primary">Ver</a>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="alert alert-info">No tienes notificaciones nuevas.</div>
        <?php endif; ?>
        
        <?php if (!empty($tareas_pendientes)): ?>
            <h6>Próximas tareas</h6>
            <ul class="list-group mb-3">
                <?php foreach ($tareas_pendientes as $t): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo htmlspecialchars($t['titulo']); ?></strong>
                            <div class="text-muted small"><?php echo htmlspecialchars($t['materia']); ?></div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted">
                                <?php $f = date_create($t['fecha_limite']); echo date_format($f, 'd M Y'); ?>
                            </small>
                            <div><a href="<?php echo $base_url; ?>/views/alumno/entregar_tarea.php?id=<?php echo urlencode($t['id']); ?>" class="btn btn-sm btn-primary mt-1">Entregar</a></div>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        <a href="<?php echo $base_url; ?>/views/alumno/perfil.php" class="btn btn-primary">Ver perfil</a>
        </div>
    </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>