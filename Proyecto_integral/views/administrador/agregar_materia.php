<?php
session_start();
include '../../Includes/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    $base_path = realpath(__DIR__ . '/../..');
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    header('Location: ' . $relative_path . '/login.php');
    exit();
}

$base_path = realpath(__DIR__ . '/../..');
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);

include '../../Includes/header.php';

 $message = '';
 $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $descripcion = trim($_POST['descripcion'] ?? '');
    $creditos = isset($_POST['creditos']) ? intval($_POST['creditos']) : 0;
    $id_departamento = isset($_POST['id_departamento']) ? intval($_POST['id_departamento']) : 1;

    if (empty($nombre)) {
        $error = 'El nombre de la materia es obligatorio.';
    } else {
        try {
            $stmt = $conexion->prepare("INSERT INTO materia (nombre, descripcion, creditos, id_departamento) VALUES (:nombre, :descripcion, :creditos, :id_departamento)");
            $stmt->execute([':nombre' => $nombre, ':descripcion' => $descripcion, ':creditos' => $creditos, ':id_departamento' => $id_departamento]);
            $message = 'Materia agregada correctamente.';
        } catch (PDOException $e) {
            $error = 'Error al agregar materia: ' . $e->getMessage();
        }
    }
}
?>

<div class="contenedor-flex-40">
    <div class="contenedor-900px">
        <h2 class="titulo-principal">Agregar Materia</h2>
        <p class="subtitulo-atenuado-85">Crea una nueva asignatura en el sistema.</p>

        <?php if ($message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Nombre de la Materia</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Descripción (opcional)</label>
                        <textarea name="descripcion" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Créditos</label>
                        <input type="number" name="creditos" class="form-control" value="5" min="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label etiqueta-formulario-negrita">Departamento</label>
                        <select name="id_departamento" class="form-select">
                            <?php
                            $deps = $conexion->query("SELECT id_departamento, nombre FROM departamento ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($deps as $d) {
                                echo '<option value="' . htmlspecialchars($d['id_departamento']) . '">' . htmlspecialchars($d['nombre']) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn-primary-custom boton-principal-oscuro boton-formulario">Guardar Materia</button>
                        <a href="<?php echo $relative_path; ?>/views/administrador/index.php" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
