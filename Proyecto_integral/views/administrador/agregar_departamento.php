<?php
session_start();
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

$base_url = '/Proyecto_integral';
//includes para conexion y header
include '../../Includes/conexion.php';
include '../../Includes/header.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    if ($nombre === '') {
        $error = 'El nombre es requerido.';
    } else {
        try {
            $ins = $conexion->prepare("INSERT INTO departamento (nombre) VALUES (:nombre)");
            $ins->execute([':nombre' => $nombre]);
            $message = 'Departamento creado.';
        } catch (PDOException $e) {
            $error = 'Error: ' . $e->getMessage();
        }
    }
}
?>


<div class="contenedor-flex-40">
  <div class="contenedor-900px">
    <h2 class="titulo-principal">Agregar Departamento</h2>
    <?php if ($message): ?><div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>

    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <form method="POST">
          <div class="mb-3">
            <label class="form-label">Nombre</label>
            <input type="text" name="nombre" class="form-control" required>
          </div>
          <div class="d-flex gap-2">
            <button class="btn-primary-custom boton-principal-oscuro boton-formulario">Guardar</button>
            <a href="<?php echo $base_url; ?>/views/administrador/departamentos_list.php" class="btn btn-secondary">Cancelar</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php include '../../Includes/footer.php'; ?>
