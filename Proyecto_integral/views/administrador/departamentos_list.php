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

$stmt = $conexion->query("SELECT id_departamento, nombre FROM departamento ORDER BY nombre");
$deps = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="contenedor-flex-40">
  <div class="contenedor-centrado-lg">
    <h2 class="titulo-principal-seccion">Departamentos</h2>
    <p class="subtitulo-atenuado-pequeño">Gestiona los departamentos académicos.</p>

    <div class="mb-3 text-end">
      <a href="<?php echo $relative_path; ?>/views/administrador/agregar_departamento.php" class="btn btn-primary">+ Nuevo Departamento</a>
      <a href="<?php echo $relative_path; ?>/views/administrador/index.php" class="btn btn-secondary">Volver</a>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <table class="table table-striped">
          <thead><tr><th>Nombre</th><th class="ancho-150">Acciones</th></tr></thead>
          <tbody>
            <?php foreach($deps as $d): ?>
              <tr>
                <td><?php echo htmlspecialchars($d['nombre']); ?></td>
                <td>
                  <a href="<?php echo $relative_path; ?>/views/administrador/editar_departamento.php?id=<?php echo $d['id_departamento']; ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                  <form method="POST" action="<?php echo $relative_path; ?>/api/admin/procesar_departamento.php" class="bloque-en-linea" onsubmit="return confirm('Eliminar departamento? Esto puede afectar materias y profesores.');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_departamento" value="<?php echo $d['id_departamento']; ?>">
                    <button class="btn btn-sm btn-outline-danger">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php include '../../Includes/footer.php'; ?>
