<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Total materias que existen
$total = $conexion->query("SELECT COUNT(*) AS cnt FROM materia")->fetch(PDO::FETCH_ASSOC)['cnt'];

// Materias sin cursos asignados 
$sin = $conexion->query("SELECT m.id_materia, m.nombre FROM materia m LEFT JOIN cursos c ON m.id_materia = c.id_materia WHERE c.id_curso IS NULL ORDER BY m.nombre")->fetchAll(PDO::FETCH_ASSOC);

// Materias por profesor 
$por_prof = $conexion->query("SELECT p.id_profesor, CONCAT(p.nombre, ' ', p.apellido) AS nombre, COUNT(c.id_curso) AS total_cursos FROM profesores p LEFT JOIN cursos c ON p.id_profesor = c.id_profesor GROUP BY p.id_profesor ORDER BY total_cursos DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado-lg">
        <h2 class="titulo-principal-seccion">Reportes: Materias y Asignaciones</h2>
        <p class="subtitulo-atenuado-pequeño margen-inferior-20">Resumen rápido de materias, asignaciones a profesores y materias sin cursos.</p>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                        <h5>Total de Materias</h5>
                        <p class="titulo-principal-grande divisor-margen-0"><?php echo (int)$total; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm p-3">
                    <h5>Materias Sin Cursos</h5>
                    <p class="titulo-principal-medio divisor-margen-0"><?php echo count($sin); ?></p>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <a href="/Proyecto_integral/views/administrador/materias_list.php" class="btn btn-secondary">Gestionar Materias</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <h5>Materias Sin Cursos Asignados</h5>
                <?php if (count($sin) === 0): ?>
                    <p class="text-success">Todas las materias están en al menos un curso.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($sin as $m): ?>
                            <li><?php echo htmlspecialchars($m['nombre']); ?> (ID: <?php echo $m['id_materia']; ?>)</li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <h5>Materias / Cursos por Profesor</h5>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Profesor</th>
                                <th>Cursos a cargo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($por_prof as $p): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                    <td><?php echo (int)$p['total_cursos']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
