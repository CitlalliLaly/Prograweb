<?php
session_start();
include '../../Includes/conexion.php';

// Verificar autenticación PRIMERO
if (!isset($_SESSION['id_usuario']) || $_SESSION['rol'] !== 'alumno') {
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

$id_alumno = $_SESSION['id_alumno'] ?? $_SESSION['id_usuario'];
$mensaje = '';
$error = '';

// Obtener datos del alumno
$sql = "SELECT nombre, apellido, correo, foto_perfil FROM alumnos WHERE id_alumno = :id";
$stmt = $conexion->prepare($sql);
$stmt->execute([':id' => $id_alumno]);
$alumno = $stmt->fetch(PDO::FETCH_ASSOC);

// Procesar cambio de foto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $archivo = $_FILES['foto'];
    
    // Validar que sea imagen
    $tipos_permitidos = ['image/jpeg', 'image/png', 'image/gif'];
    $tamaño_maximo = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($archivo['type'], $tipos_permitidos)) {
        $error = "Solo se permiten imágenes (JPG, PNG, GIF).";
    } elseif ($archivo['size'] > $tamaño_maximo) {
        $error = "La imagen no puede exceder 5MB.";
    } elseif ($archivo['error'] === UPLOAD_ERR_OK) {
        try {
            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombre_archivo = "alumno_" . $id_alumno . "_" . time() . "." . $extension;
            $ruta_archivo = $relative_path . "/assets/profile_pictures/" . $nombre_archivo;
            $ruta_completa = __DIR__ . "/../../assets/profile_pictures/" . $nombre_archivo;
            
            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
                // Actualizar en base de datos
                $update_sql = "UPDATE alumnos SET foto_perfil = :foto WHERE id_alumno = :id";
                $update_stmt = $conexion->prepare($update_sql);
                $update_stmt->execute([':foto' => $ruta_archivo, ':id' => $id_alumno]);
                
                $mensaje = "Foto de perfil actualizada exitosamente.";
                $alumno['foto_perfil'] = $ruta_archivo;
            } else {
                $error = "Error al subir la imagen. Intenta de nuevo.";
            }
        } catch (PDOException $e) {
            $error = "Error en la base de datos: " . $e->getMessage();
        }
    } else {
        $error = "Error al procesar el archivo.";
    }
}

// Crear columna foto_perfil si no existe
try {
    $check_col = $conexion->prepare("SELECT foto_perfil FROM alumnos LIMIT 1");
    $check_col->execute();
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Unknown column') !== false) {
        $conexion->exec("ALTER TABLE alumnos ADD COLUMN foto_perfil VARCHAR(255) NULL");
    }
}
?>

<div class="contenedor-flex-40">
    <div class="contenedor-centrado">

        <h2 class="titulo-principal-grande">Mi Perfil</h2>        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tarjeta de Perfil -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center relleno-40-20">
                
                <!-- Foto de Perfil -->
                <div class="seccion-contenido">
                    <?php if ($alumno['foto_perfil']): ?>
                        <img src="<?php echo htmlspecialchars($alumno['foto_perfil']); ?>" 
                             alt="Foto de perfil" 
                             class="imagen-perfil">
                    <?php else: ?>
                        <div class="imagen-perfil-placeholder">
                            <i class="fas fa-user icono-perfil"></i>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Información del Alumno -->
                <h4 class="titulo-principal">
                    <?php echo htmlspecialchars($alumno['nombre'] . ' ' . $alumno['apellido']); ?>
                </h4>
                <p class="text-muted"><?php echo htmlspecialchars($alumno['correo']); ?></p>

                <!-- Insignias recientes -->
                <div class="seccion-contenido" id="insignias">
                    <h5 class="titulo-principal-seccion margen-superior-20">Insignias</h5>
                    <div class="insignias-list">
                        <?php
                        try {
                            $sa = $conexion->prepare("SELECT a.titulo, a.icono, ua.otorgado_en FROM user_achievements ua JOIN achievements a ON a.id = ua.id_achievement WHERE ua.id_usuario = :uid ORDER BY ua.otorgado_en DESC LIMIT 6");
                            $sa->execute([':uid' => $id_alumno]);
                            $insigs = $sa->fetchAll(PDO::FETCH_ASSOC);
                            if ($insigs && count($insigs) > 0) {
                                foreach ($insigs as $in) {
                                    echo '<div class="badge-insignia"><i class="fas ' . htmlspecialchars($in['icono']) . '"></i>' . htmlspecialchars($in['titulo']) . '</div>';
                                }
                            } else {
                                echo '<p class="text-muted">Aún no tienes insignias. Entrega tareas a tiempo para obtenerlas.</p>';
                            }
                        } catch (Exception $e) {
                            // silencioso
                            echo '<p class="text-muted">No hay datos de insignias.</p>';
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>

        <!-- Formulario de Cambio de Foto -->
        <div class="card border-0 shadow-sm">
            <div class="card-header encabezado-tarjeta-claro">
                <h5 class="divisor-margen-0">📷 Cambiar Foto de Perfil</h5>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="foto" class="form-label etiqueta-formulario-negrita">Selecciona una imagen</label>
                        <input 
                            type="file" 
                            id="foto" 
                            name="foto" 
                            class="form-control" 
                            accept="image/*" 
                            required
                        >
                        <small class="text-muted">Formatos permitidos: JPG, PNG, GIF (máx. 5MB)</small>
                    </div>
                    <button type="submit" class="btn btn-primary boton-principal-oscuro ancho-100 boton-formulario">
                        Subir Foto
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
