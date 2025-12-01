<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    $base_path = realpath(__DIR__ . '/../..');
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    header('Location: ' . $relative_path . '/login.php');
    exit();
}

include '../../Includes/conexion.php';
include '../../Includes/header.php';

$base_path = realpath(__DIR__ . '/../..');
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: ' . $relative_path . '/views/administrador/materias_list.php?err=' . urlencode('ID de materia inválido'));
    exit();
}

$stmt = $conexion->prepare("SELECT * FROM materia WHERE id_materia = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$m = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$m) {
    header('Location: ' . $relative_path . '/views/administrador/materias_list.php?err=' . urlencode('Materia no encontrada'));
    exit();
}

$deps = $conexion->query("SELECT id_departamento, nombre FROM departamento ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado-lg">
        <h2 class="titulo-principal-seccion">Editar Materia</h2>
        <p class="subtitulo-atenuado-pequeño margen-inferior-20">Modifica los datos de la materia.</p>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="<?php echo $relative_path; ?>/api/admin/procesar_materia.php">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id_materia" value="<?php echo $m['id_materia']; ?>">

                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Nombre de la Materia</label>
                        <input type="text" name="nombre" class="form-control" required value="<?php echo htmlspecialchars($m['nombre']); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="4"><?php echo htmlspecialchars($m['descripcion']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Créditos</label>
                        <input type="number" name="creditos" class="form-control" value="<?php echo htmlspecialchars($m['creditos']); ?>" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Departamento</label>
                        <select name="id_departamento" class="form-select">
                            <?php foreach ($deps as $d): ?>
                                <option value="<?php echo $d['id_departamento']; ?>" <?php if ($d['id_departamento'] == $m['id_departamento']) echo 'selected'; ?>><?php echo htmlspecialchars($d['nombre']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn-primary-custom boton-formulario">Guardar Cambios</button>
                        <a href="<?php echo $relative_path; ?>/views/administrador/materias_list.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
