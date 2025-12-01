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

$msg = $_GET['msg'] ?? '';
$err = $_GET['err'] ?? '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: ' . $relative_path . '/views/administrador/departamentos_list.php');
    exit();
}

// Obtener departamento
$stmt = $conexion->prepare("SELECT id_departamento, nombre FROM departamento WHERE id_departamento = :id LIMIT 1");
$stmt->execute([':id' => $id]);
$dep = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$dep) {
    header('Location: ' . $relative_path . '/views/administrador/departamentos_list.php');
    exit();
}

// Obtener profesores del departamento
$profStmt = $conexion->prepare("SELECT id_profesor, nombre, apellido, correo FROM profesores WHERE id_departamento = :id ORDER BY nombre");
$profStmt->execute([':id' => $id]);
$profes = $profStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="contenedor-flex-40">
  <div class="contenedor-centrado-lg">
    <h2 class="titulo-principal-seccion">Editar Departamento</h2>
    <p class="subtitulo-atenuado-pequeño">Edita el nombre del departamento y revisa los profesores asignados.</p>

    <?php if ($msg): ?><div class="alert alert-success alert-dismissible fade show"><strong>Éxito:</strong> <?php echo htmlspecialchars($msg); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if ($err): ?><div class="alert alert-danger alert-dismissible fade show"><strong>Error:</strong> <?php echo htmlspecialchars($err); ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <form method="POST" action="<?php echo $relative_path; ?>/api/admin/procesar_departamento.php">
          <input type="hidden" name="action" value="update">
          <input type="hidden" name="id_departamento" value="<?php echo $dep['id_departamento']; ?>">
          <div class="mb-3">
            <label class="form-label etiqueta-formulario-negrita">Nombre</label>
            <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($dep['nombre']); ?>" required>
          </div>
          <div class="d-flex gap-2">
            <button class="btn-primary-custom boton-formulario">Guardar Cambios</button>
            <a href="<?php echo $relative_path; ?>/views/administrador/departamentos_list.php" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
      <div class="card-body">
        <h5>Profesores en este Departamento</h5>
        <?php if (count($profes) === 0): ?>
          <p class="text-muted">No hay profesores asignados a este departamento.</p>
        <?php else: ?>
          <div class="list-group">
            <?php foreach ($profes as $p): ?>
              <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                  <strong><?php echo htmlspecialchars($p['nombre'] . ' ' . $p['apellido']); ?></strong>
                  <div><small class="text-muted"><?php echo htmlspecialchars($p['correo'] ?? 'N/A'); ?></small></div>
                </div>
                <div>
                  <a href="listar_usuarios.php?highlight_prof=<?php echo $p['id_profesor']; ?>" class="btn btn-sm btn-outline-primary">Ver/Editar usuario</a>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <h5>Profesores disponibles para asignar</h5>
        <p class="text-muted">Selecciona uno o varios profesores sin departamento (o reasigna) para agregarlos a este departamento.</p>
        <?php
          // Obtener profesores sin departamento o de otros departamentos
          $availStmt = $conexion->prepare("SELECT id_profesor, nombre, apellido, correo, id_departamento FROM profesores WHERE id_profesor IS NOT NULL ORDER BY nombre");
          $availStmt->execute();
          $avail = $availStmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php if (count($avail) === 0): ?>
          <p class="text-muted">No se encontraron profesores.</p>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-sm">
              <thead><tr><th>Nombre</th><th>Correo</th><th>Departamento actual</th><th>Acción</th></tr></thead>
              <tbody>
                <?php foreach($avail as $ap): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($ap['nombre'] . ' ' . $ap['apellido']); ?></td>
                    <td><small class="text-muted"><?php echo htmlspecialchars($ap['correo'] ?? 'N/A'); ?></small></td>
                    <td>
                      <?php 
                        if ($ap['id_departamento']) {
                          $dqry = $conexion->prepare("SELECT nombre FROM departamento WHERE id_departamento = :id LIMIT 1");
                          $dqry->execute([':id' => $ap['id_departamento']]);
                          $drow = $dqry->fetch(PDO::FETCH_ASSOC);
                          echo htmlspecialchars($drow ? $drow['nombre'] : 'Desconocido');
                        } else {
                          echo '<em>Sin asignar</em>';
                        }
                      ?>
                    </td>
                    <td>
                      <?php if ($ap['id_departamento'] == $dep['id_departamento']): ?>
                        <span class="badge bg-success">Asignado</span>
                        <form method="POST" action="<?php echo $relative_path; ?>/api/admin/procesar_departamento.php" class="bloque-en-linea">
                          <input type="hidden" name="action" value="unassign">
                          <input type="hidden" name="id_departamento" value="<?php echo $dep['id_departamento']; ?>">
                          <input type="hidden" name="id_profesor" value="<?php echo $ap['id_profesor']; ?>">
                          <button class="btn btn-sm btn-outline-danger">Desasignar</button>
                        </form>
                      <?php else: ?>
                        <form method="POST" action="<?php echo $relative_path; ?>/api/admin/procesar_departamento.php" class="bloque-en-linea">
                          <input type="hidden" name="action" value="assign">
                          <input type="hidden" name="id_departamento" value="<?php echo $dep['id_departamento']; ?>">
                          <input type="hidden" name="id_profesor" value="<?php echo $ap['id_profesor']; ?>">
                          <button class="btn btn-sm btn-primary">Asignar</button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include '../../Includes/footer.php'; ?>
