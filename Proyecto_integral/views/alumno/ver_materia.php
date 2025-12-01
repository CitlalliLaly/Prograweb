<?php
session_start();
include '../../Includes/conexion.php';

// Usar URL base absoluta del proyecto
$base_url = '/Proyecto_integral';

// Validar acceso ANTES de incluir header
if (!isset($_SESSION['id_usuario']) || (strtolower($_SESSION['rol']) !== 'alumno' && strtolower($_SESSION['rol']) !== 'estudiante')) {
    header('Location: ' . $base_url . '/index.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: ' . $base_url . '/home/usuarios/index_alumno.php');
    exit();
}

// Validaciones pasadas, ahora incluir header
include '../../Includes/header.php';

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];
$id_materia = intval($_GET['id']);

// Obtener información de la materia
$sql_materia = "SELECT * FROM materia WHERE id_materia = :id_materia";
$stmt = $conexion->prepare($sql_materia);
$stmt->execute([':id_materia' => $id_materia]);
$materia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$materia) {
    header('Location: ' . $base_url . '/home/usuarios/index_alumno.php');
    exit();
}

// Obtener actividades de esta materia
$sql_actividades = "SELECT a.ID_actividad, a.titulo, a.descripcion, a.fecha_limite, a.ponderacion, a.tipo
                    FROM actividades a
                    JOIN cursos c ON a.ID_curso = c.id_curso
                    WHERE c.id_materia = :id_materia
                    ORDER BY a.fecha_limite DESC";
$stmt = $conexion->prepare($sql_actividades);
$stmt->execute([':id_materia' => $id_materia]);
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Asegurar que la tabla 'entregas' exista (para evitar errores si aún no se ha creado)
try {
    $conexion->exec("CREATE TABLE IF NOT EXISTS entregas (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        id_actividad INT NOT NULL,
        id_alumno INT NOT NULL,
        archivo_url VARCHAR(255),
        comentario TEXT,
        fecha_entrega DATETIME DEFAULT CURRENT_TIMESTAMP,
        calificacion DECIMAL(5,2) NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
} catch (Exception $e) {
    // Si no se pueden crear tablas por permisos, seguimos sin interrumpir la vista;
    // las consultas siguientes pueden fallar y serán manejadas con try/catch donde proceda.
}


function normalize_to_webpath($path) {
    if (empty($path)) return '#';
    $p = str_replace('\\', '/', $path);
    if (preg_match('#^https?://#i', $p)) return $p;
    if (preg_match('#^/#', $p)) return $p;
    $doc = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']), '/');
    if (stripos($p, $doc) === 0) {
        $web = substr($p, strlen($doc));
        if (!$web || $web[0] !== '/') $web = '/' . ltrim($web, '/');
        return $web;
    }
    if (preg_match('#^[A-Za-z]:#', $p)) {
        $parts = preg_split('#/www/#i', $p, 2);
        return isset($parts[1]) ? '/' . ltrim($parts[1], '/') : '#';
    }
    return '#';
}

// Obtener histórico de entregas del alumno para esta materia
$sql_hist = "SELECT e.id, e.id_actividad, e.archivo_url, e.comentario, e.fecha_entrega, e.calificacion, a.titulo
             FROM entregas e
             JOIN actividades a ON e.id_actividad = a.ID_actividad
             JOIN cursos c ON a.ID_curso = c.id_curso
             WHERE e.id_alumno = :id_alumno AND c.id_materia = :id_materia
             ORDER BY e.fecha_entrega DESC";
$hstmt = $conexion->prepare($sql_hist);
try {
    $hstmt->execute([':id_alumno' => $id_alumno, ':id_materia' => $id_materia]);
    $historial = $hstmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    // Si la tabla 'entregas' no existe y no podemos crearla por permisos, evitar fatal error.
    $historial = [];
}
?>

<div class="materia-container">
    <a href="<?php echo $base_url; ?>/home/usuarios/index_alumno.php" class="back-link">
        <i class="fas fa-arrow-left"></i> Volver al Dashboard
    </a>

    <div class="materia-header">
        <h1><?php echo htmlspecialchars($materia['nombre'] ?? ''); ?></h1>
        <p><?php echo htmlspecialchars($materia['descripcion'] ?? ''); ?></p>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'entrega_ok'): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            Entrega registrada correctamente.
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error']) && !empty($_GET['error'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
        </div>
    <?php endif; ?>

    <div class="materia-content">
        <section class="actividades-section">
            <h2><i class="fas fa-tasks"></i> Actividades y Tareas</h2>

            <?php if (!empty($actividades)): ?>
                <div class="actividades-list">
                    <?php
                    // Para mostrar el estado de entrega de cada actividad para este alumno,
                    // consultamos la tabla 'entregas' por cada actividad.
                    foreach($actividades as $actividad):
                        $sql_entrega = "SELECT id, archivo_url, calificacion, fecha_entrega FROM entregas WHERE id_actividad = :id_actividad AND id_alumno = :id_alumno LIMIT 1";
                        $s2 = $conexion->prepare($sql_entrega);
                        try {
                            $s2->execute([':id_actividad' => $actividad['ID_actividad'], ':id_alumno' => $id_alumno]);
                            $entrega = $s2->fetch(PDO::FETCH_ASSOC);
                        } catch (Exception $e) {
                            $entrega = null;
                        }
                    ?>
                        <div class="actividad-card">
                            <div class="actividad-header">
                                <h3><?php echo htmlspecialchars($actividad['titulo'] ?? ''); ?></h3>
                                <span class="actividad-type"><?php echo htmlspecialchars($actividad['tipo'] ?? ''); ?></span>
                            </div>

                            <div class="actividad-body">
                                <p><?php echo nl2br(htmlspecialchars($actividad['descripcion'] ?? '')); ?></p>

                                <div class="actividad-info">
                                    <div class="info-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>Fecha límite: <?php echo $actividad['fecha_limite'] ? date('d/m/Y H:i', strtotime($actividad['fecha_limite'])) : 'Sin fecha'; ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-percentage"></i>
                                        <span>Ponderación: <?php echo htmlspecialchars($actividad['ponderacion']); ?>%</span>
                                    </div>
                                </div>

                                <?php if ($entrega): ?>
                                    <div class="alert alert-success small py-2">
                                        <strong>Entregado</strong>
                                        <?php if (!empty($entrega['archivo_url'])): ?>
                                            &nbsp; - &nbsp;<?php $link = normalize_to_webpath($entrega['archivo_url'] ?? ''); ?>
                                            <a href="<?php echo htmlspecialchars($link); ?>" target="_blank" rel="noopener">Ver archivo</a>
                                        <?php endif; ?>
                                        <?php if ($entrega['calificacion'] !== null): ?>
                                            &nbsp; | &nbsp;Nota: <?php echo htmlspecialchars($entrega['calificacion']); ?>
                                        <?php else: ?>
                                            &nbsp; | &nbsp;<span class="text-muted">Pendiente de calificar</span>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <!-- Formulario de entrega -->
                                    <form action="/Proyecto_integral/procesar_entrega.php" method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="id_actividad" value="<?php echo $actividad['ID_actividad']; ?>">
                                        <input type="file" name="archivo" class="form-control form-control-sm" required accept=".pdf,.doc,.docx,.zip,.rar,.png,.jpg,.jpeg,.txt,.ppt,.pptx">
                                        <input type="text" name="comentario" class="form-control form-control-sm" placeholder="Comentario (opcional)">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane"></i> Enviar
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No hay actividades para esta materia aún.</p>
                </div>
            <?php endif; ?>
        </section>

        <section class="recursos-section">
            <h2><i class="fas fa-book"></i> Recursos</h2>
            <div class="empty-state">
                <p>Los recursos estarán disponibles pronto.</p>
            </div>
        </section>
    </div>
</div>

<!-- Histórico de entregas -->
<div class="materia-container contenedor-900px">
    <section class="actividades-section">
        <h2><i class="fas fa-history"></i> Histórico de Entregas</h2>
        <?php if (!empty($historial)): ?>
            <div class="actividades-list">
                <?php foreach($historial as $ent): ?>
                    <div class="actividad-card">
                        <div class="actividad-header">
                            <h3><?php echo htmlspecialchars($ent['titulo']); ?></h3>
                            <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($ent['fecha_entrega'])); ?></small>
                        </div>
                        <div class="actividad-body">
                            <p>
                                <?php if (!empty($ent['archivo_url'])): ?>
                                    Archivo: <?php $linkHist = normalize_to_webpath($ent['archivo_url'] ?? ''); ?>
                                    <a href="<?php echo htmlspecialchars($linkHist); ?>" target="_blank" rel="noopener">Ver</a>
                                <?php endif; ?>
                            </p>
                            <?php if (!empty($ent['comentario'])): ?>
                                <p class="small text-muted">Comentario: <?php echo htmlspecialchars($ent['comentario'] ?? ''); ?></p>
                            <?php endif; ?>
                            <?php if ($ent['calificacion'] !== null): ?>
                                <div class="badge bg-info">Calificación: <?php echo htmlspecialchars((string)($ent['calificacion'] ?? '')); ?></div>
                            <?php else: ?>
                                <div class="badge bg-secondary">Sin calificar</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <p>No has realizado entregas aún para esta materia.</p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include '../../Includes/footer.php'; ?>
