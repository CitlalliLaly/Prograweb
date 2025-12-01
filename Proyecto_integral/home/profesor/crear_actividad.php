<?php
session_start();
include '../../Includes/conexion.php';
include '../../Includes/header.php';

// Sólo profesores
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    header('Location: ../../index.php');
    exit();
}

$usuario_id = $_SESSION['id_usuario'];

// Obtener id_profesor
$stmt = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid");
$stmt->execute([':uid' => $usuario_id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$id_profesor = $row ? $row['id_profesor'] : null;

if (!$id_profesor) {
    echo "<div class='container mt-4'><div class='alert alert-warning'>No se encontró perfil de profesor asociado.</div></div>";
    include '../../Includes/footer.php';
    exit();
}

// Cursos del profesor que ellos tienen para asignar la actividad
$stmt = $conexion->prepare("SELECT id_curso, nombre_curso FROM cursos WHERE id_profesor = :id_prof");
$stmt->execute([':id_prof' => $id_profesor]);
$cursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

// Se crea la actividad
<div class="container mt-4">
    <h3>Crear Actividad</h3>

    <?php if (empty($cursos)): ?>
        <div class="alert alert-info">No tienes cursos asignados.</div>
    <?php else: ?>
        <form action="/Proyecto_integral/api/profesor/procesar_actividad.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Curso</label>
                <select name="id_curso" class="form-control" required>
                    <?php foreach ($cursos as $c): ?>
                        <option value="<?php echo $c['id_curso']; ?>"><?php echo htmlspecialchars($c['nombre_curso']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Descripción</label>
                <textarea name="descripcion" class="form-control" rows="4"></textarea>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Fecha límite</label>
                    <input type="date" name="fecha_limite" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Ponderación (%)</label>
                    <input type="number" name="ponderacion" class="form-control" min="0" max="100" step="0.01" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-control">
                        <option value="Tarea">Tarea</option>
                        <option value="Examen">Examen</option>
                        <option value="Proyecto">Proyecto</option>
                        <option value="Participacion">Participación</option>
                    </select>
                </div>
            </div>

            <button class="btn btn-primary">Crear actividad</button>
            <a href="/Proyecto_integral/views/profesor/index.php" class="btn btn-secondary">Volver</a>
        </form>
    <?php endif; ?>

</div>

<?php include '../../Includes/footer.php'; ?>
