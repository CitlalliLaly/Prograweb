<?php
session_start();
include '../../Includes/conexion.php';

// Verificar que sea administrador PRIMERO
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

include '../../Includes/header.php';
?>

<?php
$message = '';
$error = '';

// Procesar reseteo de contraseña
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_usuario'], $_POST['nueva_contrasena'])) {
    $id_usuario = intval($_POST['id_usuario']);
    $nueva_contrasena = $_POST['nueva_contrasena'];
    
    // Comprobar rol del usuario objetivo
    $check = $conexion->prepare("SELECT rol FROM usuarios WHERE id_usuario = :id LIMIT 1");
    $check->execute([':id' => $id_usuario]);
    $target = $check->fetch(PDO::FETCH_ASSOC);

    if (!$target) {
        $error = 'Usuario no encontrado.';
    } else {
        $target_rol = strtolower($target['rol']);
        $current_id = $_SESSION['id_usuario'];

        // Impedir que un admin modifique a otro admin (Solo puede modificarse a sí mismo)
        if ($target_rol === 'administrador' && $id_usuario !== $current_id) {
            $error = 'No estás autorizado para modificar a otro administrador.';
        } else {
            if (strlen($nueva_contrasena) < 6) {
                $error = 'La contraseña debe tener al menos 6 caracteres.';
            } else {
                $password_hash = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
                $sql = "UPDATE usuarios SET password = :password WHERE id_usuario = :id";
                $stmt = $conexion->prepare($sql);
                $stmt->execute([':password' => $password_hash, ':id' => $id_usuario]);
                $message = 'Contraseña actualizada correctamente.';
            }
        }
    }
}

// Obtener todos los usuarios registrados en la BDD
$sql = "SELECT id_usuario, username, rol FROM usuarios ORDER BY rol, username";
$usuarios = $conexion->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="main-content">
    <h2 class="dashboard-title">Resetear Contraseña de Usuarios</h2>
    <p class="text-muted">Herramienta para administradores: resetea la contraseña de cualquier usuario.</p>

    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo $message; ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="tabla-responsiva">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($usuario['username']); ?></td>
                        <td><span class="badge bg-info"><?php echo $usuario['rol']; ?></span></td>
                        <td>
                            <?php
                                $is_admin_target = strtolower($usuario['rol']) === 'administrador';
                                $is_self = $usuario['id_usuario'] == 
                                    (isset($_SESSION['id_usuario']) ? $_SESSION['id_usuario'] : 0);
                            ?>
                            <?php if ($is_admin_target && !$is_self): ?>
                                <button class="btn btn-sm btn-secondary" disabled title="No permitido modificar a otro administrador">Administrador protegido</button>
                            <?php else: ?>
                                <button class="btn btn-sm btn-warning" onclick="mostrarFormulario(<?php echo $usuario['id_usuario']; ?>, '<?php echo htmlspecialchars($usuario['username']); ?>')">
                                    Resetear Contraseña
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para resetear contraseña -->
<div id="resetModal" class="modal-superpuesto">
    <div class="contenido-modal">
        <h3 id="modalTitle">Resetear Contraseña</h3>
        <form method="POST">
            <input type="hidden" id="userId" name="id_usuario">
            
            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" class="form-control" required>
            </div>
            
            <div class="flex-brecha-10">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function mostrarFormulario(userId, username) {
    document.getElementById('userId').value = userId;
    document.getElementById('modalTitle').textContent = 'Resetear contraseña de: ' + username;
    document.getElementById('resetModal').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('resetModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('resetModal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}
</script>

<?php include '../../Includes/footer.php'; ?>
