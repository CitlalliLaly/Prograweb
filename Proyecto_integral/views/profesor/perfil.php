<?php
session_start();
include '../../Includes/conexion.php';

// Verificar rol
if (!isset($_SESSION['id_usuario']) || !in_array(strtolower($_SESSION['rol']), ['profesor','maestro'])) {
    $base_path = realpath(__DIR__ . '/../..');
    $relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
    header('Location: ' . $relative_path . '/login.php');
    exit();
}

// Mostrar buscador en perfiles de usuario
$show_search = true;
$base_path = realpath(__DIR__ . '/../..');
$relative_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $base_path);
include '../../Includes/header.php';

$mensaje = '';
$error = '';

// Determinar id_profesor
$id_profesor = $_SESSION['id_profesor'] ?? null;
if (!$id_profesor) {
    $q = $conexion->prepare("SELECT id_profesor FROM usuarios WHERE id_usuario = :uid LIMIT 1");
    $q->execute([':uid' => $_SESSION['id_usuario']]);
    $row = $q->fetch(PDO::FETCH_ASSOC);
    $id_profesor = $row ? $row['id_profesor'] : null;
}

if (!$id_profesor) {
    echo '<div class="container mt-4"><div class="alert alert-warning">No se encontró perfil de profesor asociado. Contacta al administrador.</div></div>';
    include '../../Includes/footer.php';
    exit();
}

// Obtener datos del profesor incluyendo departamento
$sql = "SELECT p.nombre, p.apellido, p.correo, p.foto_perfil, p.id_departamento, d.nombre AS departamento
    FROM profesores p LEFT JOIN departamento d ON p.id_departamento = d.id_departamento
    WHERE p.id_profesor = :id LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id_profesor]);
$prof = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar subida
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
        $nombre_archivo = "profesor_" . $id_profesor . "_" . time() . "." . $extension;
        $ruta_archivo = $relative_path . "/assets/profile_pictures/" . $nombre_archivo;
        $ruta_completa = __DIR__ . "/../../assets/profile_pictures/" . $nombre_archivo;

        if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
            $update = $conexion->prepare("UPDATE profesores SET foto_perfil = :foto WHERE id_profesor = :id");
            $update->execute([':foto' => $ruta_archivo, ':id' => $id_profesor]);
            $mensaje = 'Foto de perfil actualizada exitosamente.';
            $prof['foto_perfil'] = $ruta_archivo;
        } else {
            $error = 'Error al subir la imagen. Intenta de nuevo.';
        }
    } else {
        $error = 'Error al procesar el archivo.';
    }
}

// Asegurar columna foto_perfil (por si la migración no corrió)
try {
    $check = $conexion->prepare("SELECT foto_perfil FROM profesores LIMIT 1");
    $check->execute();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        $conexion->exec("ALTER TABLE profesores ADD COLUMN foto_perfil VARCHAR(255) NULL");
    }
}
?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado">
        <h2 class="titulo-principal-medio">Mi Perfil (Profesor)</h2>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center relleno-40-20">
                <div class="margen-inferior-30">
                    <?php if (!empty($prof['foto_perfil'])): ?>
                        <img src="<?php echo htmlspecialchars($prof['foto_perfil']); ?>" alt="Foto" class="imagen-perfil">
                    <?php else: ?>
                        <div class="imagen-perfil-placeholder">
                            <i class="fas fa-user icono-perfil"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <h4 class="titulo-principal-medio"><?php echo htmlspecialchars($prof['nombre'] . ' ' . $prof['apellido']); ?></h4>
                <p class="text-muted"><?php echo htmlspecialchars($prof['correo'] ?? ''); ?></p>
                <p class="text-muted"><strong>Departamento:</strong> <?php echo htmlspecialchars($prof['departamento'] ?? 'Sin asignar'); ?></p>
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
