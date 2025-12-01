<?php
session_start();
include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Solo profesores
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

// Obtener id_profesor
$pstmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid LIMIT 1");
$pstmt->execute([':uid' => $_SESSION['id_usuario']]);
$prow = $pstmt->fetch(PDO::FETCH_ASSOC);
$id_prof = $prow ? $prow['id_profesor'] : null;

if (!$id_prof) {
    echo "<div class='container mt-4'><div class='alert alert-danger'>Perfil de profesor no encontrado.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

$curso_filter = isset($_GET['curso']) ? intval($_GET['curso']) : null;

$sql = "SELECT a.ID_actividad, a.titulo, a.fecha_limite, c.id_curso, c.nombre_curso
        FROM actividades a
        JOIN cursos c ON a.ID_curso = c.id_curso
        WHERE c.id_profesor = :idp";
$params = [':idp' => $id_prof];
if ($curso_filter) {
    $sql .= " AND c.id_curso = :cid";
    $params[':cid'] = $curso_filter;
}
$sql .= " ORDER BY c.nombre_curso, a.fecha_limite DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Actividades publicadas</h2>
        <div>
            <a href="/Proyecto_integral/home/profesor/crear_actividad.php" class="btn btn-primary">Crear actividad</a>
            <a href="/Proyecto_integral/home/profesor/actividades.php" class="btn btn-outline-secondary">Volver</a>
        </div>
    </div>

    <?php if (empty($rows)): ?>
        <div class="alert alert-info">No hay actividades publicadas.</div>
    <?php else: ?>
        <div class="list-group">
            <?php foreach ($rows as $r): ?>
                <div class="list-group-item">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong><?php echo htmlspecialchars($r['titulo']); ?></strong>
                            <div class="text-muted small"><?php echo htmlspecialchars($r['nombre_curso']); ?></div>
                        </div>
                        <div class="text-end">
                            <div class="small text-muted"><?php echo htmlspecialchars($r['fecha_limite']); ?></div>
                            <a href="/Proyecto_integral/home/profesor/calificar.php?id_curso=<?php echo urlencode($r['id_curso']); ?>&id_actividad=<?php echo urlencode($r['ID_actividad']); ?>" class="btn btn-sm btn-outline-primary mt-2">Calificar</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<?php include '../../Includes/footer.php'; ?>
