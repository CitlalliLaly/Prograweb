<?php
session_start();
include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Permitir solo a profesores SOLO PROFESORES
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

// Obtener id_profesor del usuario en sesión
$pstmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid LIMIT 1");
$pstmt->execute([':uid' => $_SESSION['id_usuario']]);
$prow = $pstmt->fetch(PDO::FETCH_ASSOC);
$id_prof = $prow ? $prow['id_profesor'] : null;

if (!$id_prof) {
    echo "<div class=\"container\"><div class=\"alert alert-danger\">No se encontró perfil de profesor para este usuario.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

// Obtener alumnos por curso dictado por el profesor
$sql = "SELECT c.id_curso, c.nombre_curso, a.id_alumno, a.nombre AS alumno_nombre, a.apellido AS alumno_apellido, ins.estado
        FROM cursos c
        LEFT JOIN inscripciones ins ON c.id_curso = ins.ID_curso AND ins.estado = 'Aprobado'
        LEFT JOIN alumnos a ON ins.ID_alumno = a.id_alumno
        WHERE c.id_profesor = :id_prof
        ORDER BY c.nombre_curso, a.apellido, a.nombre";

$stmt = $conexion->prepare($sql);
$stmt->execute([':id_prof' => $id_prof]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Agrupar por curso para mantener un orden lógico
$cursos = [];
foreach ($rows as $r) {
    $cid = $r['id_curso'];
    if (!isset($cursos[$cid])) {
        $cursos[$cid] = [
            'nombre' => $r['nombre_curso'],
            'alumnos' => []
        ];
    }
    if ($r['id_alumno']) {
        $cursos[$cid]['alumnos'][] = [
            'id' => $r['id_alumno'],
            'nombre' => trim(($r['alumno_nombre'] ?? '') . ' ' . ($r['alumno_apellido'] ?? '')),
            'estado' => $r['estado']
        ];
    }
}

?>
<div class="container mt-4">
    <h2>Tus Cursos y Alumnos</h2>
    <?php if (empty($cursos)): ?>
        <div class="alert alert-info">No tienes cursos asignados o no hay alumnos aprobados aún.</div>
    <?php else: ?>
        <?php foreach ($cursos as $cid => $curso): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <strong><?php echo htmlspecialchars($curso['nombre'] ?? 'Curso sin nombre'); ?></strong>
                    <small class="text-muted"> (ID: <?php echo htmlspecialchars($cid); ?>)</small>
                </div>
                <div class="card-body">
                    <?php if (empty($curso['alumnos'])): ?>
                        <p class="mb-0 text-muted">No hay alumnos aprobados en este curso.</p>
                    <?php else: ?>
                        <ul class="list-group">
                            <?php foreach ($curso['alumnos'] as $al): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <?php echo htmlspecialchars($al['nombre']); ?>
                                        <br><small class="text-muted">ID: <?php echo htmlspecialchars($al['id']); ?></small>
                                    </div>
                                    <span class="badge badge-primary badge-pill"><?php echo htmlspecialchars($al['estado'] ?? 'Aprobado'); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../../Includes/footer.php'; ?>
