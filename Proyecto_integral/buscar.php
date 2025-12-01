<?php
session_start();
include __DIR__ . '/Includes/conexion.php';
$page_title = 'Buscar';
include __DIR__ . '/Includes/header.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = ['cursos' => [], 'actividades' => [], 'alumnos' => []];

if ($q !== '') {
    $like = '%' . $q . '%';
    try {
        // Buscar cursos que existen en la BDD
        $stmt = $conexion->prepare("SELECT id_curso, nombre_curso FROM cursos WHERE nombre_curso LIKE :q LIMIT 50");
        $stmt->execute([':q' => $like]);
        $results['cursos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar actividades que existen en la BDD
        $stmt = $conexion->prepare("SELECT ID_actividad, titulo, ID_curso FROM actividades WHERE titulo LIKE :q LIMIT 100");
        $stmt->execute([':q' => $like]);
        $results['actividades'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Buscar alumnos con nombre o apellido que son iguales en la base de datos
        $stmt = $conexion->prepare("SELECT id_alumno, nombre, apellido FROM alumnos WHERE CONCAT(nombre, ' ', apellido) LIKE :q OR nombre LIKE :q OR apellido LIKE :q LIMIT 100");
        $stmt->execute([':q' => $like]);
        $results['alumnos'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo '<div class="container mt-4"><div class="alert alert-danger">Error en búsqueda: ' . htmlspecialchars($e->getMessage()) . '</div></div>';
        include __DIR__ . '/Includes/footer.php';
        exit();
    }
}
?>
<div class="container mt-4">
    <h2>Búsqueda</h2>
    <form class="mb-3" action="<?php echo $base_path; ?>buscar.php" method="GET">
        <div class="input-group">
            <input type="search" name="q" class="form-control" placeholder="Buscar cursos, actividades o alumnos..." value="<?php echo htmlspecialchars($q); ?>">
            <button class="btn btn-primary" type="submit">Buscar</button>
        </div>
    </form>

    <?php if ($q === ''): ?>
        <div class="alert alert-info">Escribe algo para buscar.</div>
    <?php else: ?>

        <h4>Cursos (<?php echo count($results['cursos']); ?>)</h4>
        <?php if (empty($results['cursos'])): ?>
            <p class="text-muted">No se encontraron cursos.</p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($results['cursos'] as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <a href="<?php echo $base_path; ?>views/profesor/ver_materia.php?id_curso=<?php echo urlencode($c['id_curso']); ?>"><?php echo htmlspecialchars($c['nombre_curso']); ?></a>
                            <br><small class="text-muted">ID: <?php echo htmlspecialchars($c['id_curso']); ?></small>
                        </div>
                        <a class="btn btn-sm btn-outline-primary" href="<?php echo $base_path; ?>home/profesor/ver_actividades.php?curso=<?php echo urlencode($c['id_curso']); ?>">Ver actividades</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <h4>Actividades (<?php echo count($results['actividades']); ?>)</h4>
        <?php if (empty($results['actividades'])): ?>
            <p class="text-muted">No se encontraron actividades.</p>
        <?php else: ?>
            <div class="list-group mb-3">
                <?php foreach ($results['actividades'] as $a): ?>
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong><?php echo htmlspecialchars($a['titulo']); ?></strong>
                                <div class="small text-muted">Curso ID: <?php echo htmlspecialchars($a['ID_curso']); ?> | Actividad ID: <?php echo htmlspecialchars($a['ID_actividad']); ?></div>
                            </div>
                            <div>
                                <a class="btn btn-sm btn-outline-primary" href="/Proyecto_integral/home/profesor/calificar.php?id_curso=<?php echo urlencode($a['ID_curso']); ?>&id_actividad=<?php echo urlencode($a['ID_actividad']); ?>">Ver/Calificar</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h4>Alumnos (<?php echo count($results['alumnos']); ?>)</h4>
        <?php if (empty($results['alumnos'])): ?>
            <p class="text-muted">No se encontraron alumnos.</p>
        <?php else: ?>
            <ul class="list-group mb-3">
                <?php foreach ($results['alumnos'] as $al): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <?php echo htmlspecialchars($al['nombre'] . ' ' . $al['apellido']); ?>
                        </div>
                        <a class="btn btn-sm btn-outline-secondary" href="/Proyecto_integral/views/alumno/perfil.php?id_alumno=<?php echo urlencode($al['id_alumno']); ?>">Ver perfil</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php include __DIR__ . '/Includes/footer.php'; ?>
