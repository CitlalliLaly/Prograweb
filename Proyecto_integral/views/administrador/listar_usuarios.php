<?php
session_start();
include '../../Includes/conexion.php';

// Verificar que sea administrador
if (!isset($_SESSION['id_usuario']) || ($_SESSION['rol'] ?? '') !== 'administrador') {
    header('Location: ../../login.php');
    exit();
}

include '../../Includes/header.php';

// Asegurarse de que la columna 'activo' exista
try {
    $colCheck = $conexion->prepare("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'activo'");
    $colCheck->execute();
    if ($colCheck->fetchColumn() == 0) {
        // Añadir columna activo
        $conexion->exec("ALTER TABLE usuarios ADD COLUMN activo TINYINT(1) NOT NULL DEFAULT 1");
    }
} catch (Exception $e) {
    // Si falla por permisos, ignoramos y continuamos; el listado seguirá funcionando sin filtro activo.
}

// Manejar acciones POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'deactivate' && isset($_POST['id_usuario'])) {
    $targetId = intval($_POST['id_usuario']);
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET activo = 0 WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $targetId]);
        $success = 'Usuario marcado como inactivo.';
    } catch (Exception $e) {
        $error = 'Error al desactivar usuario: ' . $e->getMessage();
    }
}

// Reactivar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reactivate' && isset($_POST['id_usuario'])) {
    $targetId = intval($_POST['id_usuario']);
    try {
        $stmt = $conexion->prepare("UPDATE usuarios SET activo = 1 WHERE id_usuario = :id LIMIT 1");
        $stmt->execute([':id' => $targetId]);
        $success = 'Usuario reactivado correctamente.';
    } catch (Exception $e) {
        $error = 'Error al reactivar usuario: ' . $e->getMessage();
    }
}

// Resetear contraseña siendo admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset_password' && isset($_POST['id_usuario'], $_POST['nueva_contrasena'])) {
    $targetId = intval($_POST['id_usuario']);
    $newPass = trim($_POST['nueva_contrasena']);
    if (strlen($newPass) < 6) {
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    } else {
        try {
            // Evitar que un admin modifique a otro admin (salvo sí mismo)
            $check = $conexion->prepare("SELECT rol FROM usuarios WHERE id_usuario = :id LIMIT 1");
            $check->execute([':id' => $targetId]);
            $t = $check->fetch(PDO::FETCH_ASSOC);
            $targetRol = $t ? strtolower($t['rol']) : '';
            $currentId = $_SESSION['id_usuario'];
            if ($targetRol === 'administrador' && $targetId !== $currentId) {
                $error = 'No estás autorizado para modificar a otro administrador.';
            } else {
                $hash = password_hash($newPass, PASSWORD_BCRYPT);
                $upd = $conexion->prepare("UPDATE usuarios SET password = :pw WHERE id_usuario = :id");
                $upd->execute([':pw' => $hash, ':id' => $targetId]);
                $success = 'Contraseña actualizada correctamente.';
            }
        } catch (Exception $e) {
            $error = 'Error al resetear contraseña: ' . $e->getMessage();
        }
    }
}

// Obtener usuarios: por defecto solo activos, a menos que se busque (q)
$q = trim($_GET['q'] ?? '');
try {
    if ($q !== '') {
        $sql = "SELECT id_usuario, username, rol, activo FROM usuarios WHERE username LIKE :q OR rol LIKE :q ORDER BY rol, username";
        $stmt = $conexion->prepare($sql);
        $like = '%' . $q . '%';
        $stmt->execute([':q' => $like]);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // mostrar solo activos
        $sql = "SELECT id_usuario, username, rol, activo FROM usuarios WHERE activo = 1 ORDER BY rol, username";
        $stmt = $conexion->query($sql);
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) {
    $usuarios = [];
    $error = 'Error al obtener usuarios: ' . $e->getMessage();
}
?>

<div class="main-content">
    <h2 class="dashboard-title">Listado de Usuarios</h2>
    <p class="text-muted">Usuarios registrados en el sistema (solo administradores).</p>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <div class="tabla-responsiva">
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($u['id_usuario']); ?></td>
                        <td><?php echo htmlspecialchars($u['username']); ?></td>
                        <td><?php echo htmlspecialchars($u['rol']); ?></td>
                        <td><?php echo (isset($u['activo']) && $u['activo'] == 1) ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-secondary">Inactivo</span>'; ?></td>
                        <td>
                            <!-- Resetear contraseña  -->
                            <button class="btn btn-sm btn-warning" onclick="openResetModal(<?php echo $u['id_usuario']; ?>, '<?php echo htmlspecialchars($u['username'], ENT_QUOTES); ?>')">Resetear</button>
                            <a href="/Proyecto_integral/views/administrador/editar_usuario.php?userid=<?php echo urlencode($u['id_usuario']); ?>" class="btn btn-sm btn-secondary">Editar</a>
                            <!-- Acciones de estado: Desactivar o Reactivar -->
                            <?php if (!isset($u['activo']) || $u['activo'] == 1): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas desactivar este usuario?');">
                                    <input type="hidden" name="action" value="deactivate">
                                    <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                                    <button class="btn btn-sm btn-danger">Eliminar</button>
                                </form>
                            <?php else: ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('¿Confirmas reactivar este usuario?');">
                                    <input type="hidden" name="action" value="reactivate">
                                    <input type="hidden" name="id_usuario" value="<?php echo $u['id_usuario']; ?>">
                                    <button class="btn btn-sm btn-success">Reactivar</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- resetear contraseña -->
<div id="resetModal" class="modal-superpuesto">
    <div class="contenido-modal">
        <h3 id="modalTitle">Resetear Contraseña</h3>
        <form method="POST">
            <input type="hidden" id="reset_userId" name="id_usuario">
            <input type="hidden" name="action" value="reset_password">
            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" class="form-control" required>
            </div>
            <div class="flex-brecha-10">
                <button type="submit" class="btn btn-success">Actualizar</button>
                <button type="button" class="btn btn-secondary" onclick="closeResetModal()">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function openResetModal(userId, username) {
    document.getElementById('reset_userId').value = userId;
    document.getElementById('modalTitle').textContent = 'Resetear contraseña de: ' + username;
    document.getElementById('resetModal').style.display = 'block';
}
function closeResetModal() {
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
