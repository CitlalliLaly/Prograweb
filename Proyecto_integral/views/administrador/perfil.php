<?php
session_start();
include '../../Includes/conexion.php';

// Verificar rol
if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) !== 'administrador') {
    header('Location: /Proyecto_integral/login.php');
    exit();
}

$show_search = true; // Mostrar la barra de búsqueda solo en páginas de perfil por si las dudas
include '../../Includes/header.php';

$mensaje = '';
$error = '';

// Determinar el id_admin
$id_admin = $_SESSION['id_admin'] ?? null;
if (!$id_admin) {
    $q = $conexion->prepare("SELECT id_admin FROM usuarios WHERE id_usuario = :uid LIMIT 1");
    $q->execute([':uid' => $_SESSION['id_usuario']]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_admin = $row ? $row['id_admin'] : null;
}

if (!$id_admin) {
    echo '<div class="container mt-4"><div class="alert alert-warning">No se encontró perfil de administrador asociado. Contacta al super-admin.</div></div>';
    include '../../Includes/footer.php';
    exit();
}

// Obtener datos del admin en cuestion
$sql = "SELECT nombre, apellido, correo, foto_perfil FROM administradores WHERE id_admin = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id_admin]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar subida de las fotos de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $archivo = $_FILES['foto'];
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    $tamaño_maximo = 5 * 1024 * 1024;

    if (!in_array($archivo['type'], $tipos_permitidos)) {
        $error = 'Solo se permiten imágenes (JPG, PNG, GIF).';
    } elseif ($archivo['size'] > $tamaño_maximo) {
        $error = 'La imagen no puede exceder 5MB.';
    } elseif ($archivo['error'] === UPLOAD_ERR_OK) {
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "admin_" . $id_admin . "_" . time() . "." . $extension;
        $ruta_archivo = "/Proyecto_integral/assets/profile_pictures/" . $nombre_archivo;
        $ruta_completa = __DIR__ . "/../../assets/profile_pictures/" . $nombre_archivo;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            $update = $conexion->prepare("UPDATE administradores SET foto_perfil = :foto WHERE id_admin = :id");
            $update->execute([':foto' => $ruta_archivo, ':id' => $id_admin]);
            $mensaje = 'Foto de perfil actualizada exitosamente.';
            $admin['foto_perfil'] = $ruta_archivo;
        } else {
            $error = 'Error al subir la imagen. Intenta de nuevo.';
        }
    } else {
        $error = 'Error al procesar el archivo.';
    }
}

// Asegurarse de que la columna foto perfil si existe
try {
    $check = $conexion->prepare("SELECT foto_perfil FROM administradores LIMIT 1");
    $check->execute();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        $conexion->exec("ALTER TABLE administradores ADD COLUMN foto_perfil VARCHAR(255) NULL");
    }
}
?>


<div class="contenedor-flex-40">
    <div class="contenedor-centrado">
        <h2 class="titulo-principal-medio">Mi Perfil (Administrador)</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center relleno-40-20">
                <div class="margen-inferior-30">
                    <?php if (!empty($admin['foto_perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($admin['foto_perfil']); ?>" alt="Foto" class="imagen-perfil">
                    <?php else: ?>
                        <div class="imagen-perfil-placeholder">
                            <i class="fas fa-user-shield icono-perfil"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h4 class="titulo-principal-medio"><?php echo htmlspecialchars($admin['nombre'] . ' ' . $admin['apellido']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($admin['correo'] ?? ''); ?></p>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header encabezado-tarjeta-claro">
                <h5 class="divisor-margen-0">📷 Cambiar Foto de Perfil</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="foto" class="form-label">Selecciona una imagen</label>
                        <input type="file" id="foto" name="foto" class="form-control" accept="image/*" required>
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF (máx. 5MB)</small>
                    </div>
                    <button type="submit" class="btn-primary-custom boton-formulario ancho-100">Subir Foto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
