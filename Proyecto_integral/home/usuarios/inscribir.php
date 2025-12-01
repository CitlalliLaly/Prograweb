include '../../Includes/header.php';
<?php
include '../../Includes/header.php';

if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) != 'alumno') {
    $base_path = realpath(__DIR__ . '/../..');
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    header('Location: ' . $relative_path . '/home/usuarios/index_alumno.php');
    exit();
}

$base_path = realpath(__DIR__ . '/../..');
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];

// Muestra los cursos disponibles que no tengan una inscripcion anterior
$sql = "SELECT c.id_curso, c.nombre_curso, m.nombre as materia
        FROM cursos c
        JOIN materia m ON c.id_materia = m.id_materia
        WHERE NOT EXISTS (SELECT 1 FROM inscripciones ins WHERE ins.ID_curso = c.id_curso AND ins.ID_alumno = :id_alumno)";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id_alumno' => $id_alumno]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Muentra información de depuración si se solicita
if (!empty($_GET['debug'])) {
    $totalCursosStmt = $conexion->query("SELECT COUNT(*) FROM cursos");
    $totalCursos = (int)$totalCursosStmt->fetchColumn();

    $inscritosStmt = $conexion->prepare("SELECT COUNT(*) FROM inscripciones WHERE ID_alumno = :id_alumno");
    $inscritosStmt->execute([':id_alumno' => $id_alumno]);
    $inscritosCount = (int)$inscritosStmt->fetchColumn();

    echo '<div class="container mt-3"><pre style="white-space:pre-wrap;background:#fff;padding:10px;border:1px solid #ddd;">';
    echo "id_alumno (sesión): " . htmlspecialchars($id_alumno) . "\n";
    echo "Cursos encontrados (disponibles): " . count($cursos) . "\n";
    echo "Total cursos en la plataforma: " . $totalCursos . "\n";
    echo "Inscripciones registradas para el alumno: " . $inscritosCount . "\n\n";
    echo "Consulta SQL usada para obtener disponibles:\n" . htmlspecialchars($sql) . "\n\n";
    echo "Resultados (primeros 10):\n";
    print_r(array_slice($cursos,0,10));
    echo '</pre></div>';
}

?>

<div class="container mt-4">
    <h3>Inscribirse a un curso</h3>
    <?php if (!empty($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>
    <?php if (!empty($_GET['err'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['err']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_ok'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['mensaje_ok']; unset($_SESSION['mensaje_ok']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['mensaje_error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['mensaje_error']; unset($_SESSION['mensaje_error']); ?></div>
    <?php endif; ?>
    <?php if (empty($cursos)): ?>
        <div class="alert alert-info">No hay cursos disponibles para inscribirte o ya solicitaste inscripción.</div>
    <?php else: ?>
                        <table class="table">
            <thead>
                <tr><th>Curso</th><th>Materia</th><th>Accion</th></tr>
            </thead>
            <tbody>
                <?php foreach ($cursos as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nombre_curso']); ?></td>
                        <td><?php echo htmlspecialchars($c['materia']); ?></td>
                        <td>
                            <form action="/Proyecto_integral/api/alumno/procesar_solicitud_inscripcion.php" method="POST">
                                <input type="hidden" name="id_curso" value="<?php echo $c['id_curso']; ?>">
                                <button class="btn btn-primary">Solicitar inscripción</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="/Proyecto_integral/home/usuarios/index_alumno.php" class="btn btn-secondary">Volver</a>
</div>

<?php include '../../Includes/footer.php'; ?>
