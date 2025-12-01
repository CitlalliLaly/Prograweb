<?php
session_start();
include '../../Includes/conexion.php';

include '../../Includes/header.php';

// Sólo profesores SOLO PROFESORES
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

$usuario_id = $_SESSION['id_usuario'];

// Obtener id_profesor  otra verificacion
$stmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid");
$stmt->execute([':uid' => $usuario_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id_profesor = $row ? $row['id_profesor'] : null;

if (!$id_profesor) {
    echo "<div class='container mt-4'><div class='alert alert-warning'>No se encontró perfil de profesor asociado.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

// Si no se proporcionó curso se listan los cursos que tiene ya que aun no elige uno a calificar
if (!isset($_GET['id_curso'])) {
    $stmt = $conexion->prepare("SELECT id_curso, nombre_curso FROM cursos WHERE id_profesor = :id_prof");
    $stmt->execute([':id_prof' => $id_profesor]);
    $cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <div class="container mt-4">
        <h3>Selecciona un curso para calificar</h3>
        <?php if (empty($cursos)): ?>
            <div class="alert alert-info">No tienes cursos asignados.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($cursos as $c): ?>
                    <li><a href="/Proyecto_integral/home/profesor/calificar.php?id_curso=<?php echo $c['id_curso']; ?>"><?php echo htmlspecialchars($c['nombre_curso']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="../../views/profesor/index.php" class="btn btn-secondary">Volver</a>
    </div>
    <?php
    include '../../Includes/footer.php';
    exit();
}

$id_curso = intval($_GET['id_curso']);

// Listar actividades del curso que se han dejado hasta ese momento
$stmt = $conexion->prepare("SELECT ID_actividad, titulo FROM actividades WHERE ID_curso = :id_curso ORDER BY fecha_limite DESC");
$stmt->execute([':id_curso' => $id_curso]);
$actividades = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si no se proporcionó actividad, se muestra la lista
if (!isset($_GET['id_actividad'])) {
    ?>
    <div class="container mt-4">
        <h3>Actividades del curso</h3>
        <?php if (empty($actividades)): ?>
            <div class="alert alert-info">No hay actividades para este curso.</div>
        <?php else: ?>
            <ul>
                <?php foreach ($actividades as $a): ?>
                    <li><a href="/Proyecto_integral/home/profesor/calificar.php?id_curso=<?php echo $id_curso; ?>&id_actividad=<?php echo $a['ID_actividad']; ?>"><?php echo htmlspecialchars($a['titulo']); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        <a href="/Proyecto_integral/home/profesor/calificar.php" class="btn btn-secondary">Volver</a>
    </div>
    <?php
    include '../../Includes/footer.php';
    exit();
}

$id_actividad = intval($_GET['id_actividad']);

// Obtener alumnos inscritos y aprobados en el curso
$sql = "SELECT ins.ID_inscripcion, a.id_alumno, a.nombre, a.apellido
        FROM inscripciones ins
        JOIN alumnos a ON ins.ID_alumno = a.id_alumno
        WHERE ins.ID_curso = :id_curso AND ins.estado = 'Aprobado'";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id_curso' => $id_curso]);
$alumnos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="container mt-4">
    <h3>Calificar actividad</h3>
    <p>Actividad ID: <?php echo $id_actividad; ?></p>

    <?php if (empty($alumnos)): ?>
        <div class="alert alert-info">No hay alumnos aprobados en este curso.</div>
    <?php else: ?>
        <form action="/Proyecto_integral/api/profesor/procesar_calificacion.php" method="POST">
            <input type="hidden" name="id_actividad" value="<?php echo $id_actividad; ?>">
            <input type="hidden" name="id_curso" value="<?php echo $id_curso; ?>">
            <table class="table">
                <thead>
                    <tr>
                        <th>Alumno</th>
                        <th>Calificación</th>
                        <th>Comentario</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alumnos as $al): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($al['nombre'] . ' ' . $al['apellido']); ?></td>
                            <td>
                                <input type="hidden" name="inscripcion_id[]" value="<?php echo $al['ID_inscripcion']; ?>">
                                <input type="number" name="calificacion[]" step="0.01" min="0" max="100" class="form-control">
                            </td>
                            <td>
                                <input type="text" name="comentario[]" class="form-control">
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <button class="btn btn-primary">Guardar calificaciones</button>
        </form>
    <?php endif; ?>

    <a href="/Proyecto_integral/home/profesor/calificar.php" class="btn btn-secondary mt-3">Volver</a>
</div>

<?php include '../../Includes/footer.php'; ?>
```
