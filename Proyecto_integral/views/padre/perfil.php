<?php
session_start();
$page_title = "Perfil Padre | CUSJ";

// Verificar que sea padre
if (!isset($_SESSION['id_usuario']) || strtolower($_SESSION['rol']) !== 'padre') {
    header('Location: ../../login.php');
    exit();
}

include '../../Includes/header.php';
include '../../Includes/conexion.php';

// Obtener id_padre del usuario actual
$id_usuario = $_SESSION['id_usuario'];
$sql_padre = "SELECT id_padre FROM usuarios WHERE id_usuario = :id_usuario";
$stmt_padre = $conexion->prepare($sql_padre);
$stmt_padre->execute([':id_usuario' => $id_usuario]);
$padre_row = $stmt_padre->fetch(PDO::FETCH_ASSOC);

if (!$padre_row) {
    echo "<div class='alert alert-danger'>Error: No se pudo identificar como padre.</div>";
    include '../../Includes/footer.php';
    exit();
}

$id_padre = $padre_row['id_padre'];

// Obtener información del padre
$sql_info = "SELECT id_padre, nombre, apellido, correo, telefono, foto FROM padres WHERE id_padre = :id_padre";
$stmt_info = $conexion->prepare($sql_info);
$stmt_info->execute([':id_padre' => $id_padre]);
$info_padre = $stmt_info->fetch(PDO::FETCH_ASSOC);

// Procesar actualización de datos
$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    
    if (empty($nombre) || empty($apellido) || empty($correo)) {
        $error = "Nombre, apellido y email son requeridos.";
    } else if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El email no es válido.";
    } else {
        try {
            // Procesar foto si se cargó
            $foto_path = $info_padre['foto'];
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($_FILES['foto']['type'], $allowed_types)) {
                    $error = "Solo se permiten imágenes (JPG, PNG, GIF).";
                } else if ($_FILES['foto']['size'] > 5000000) { // 5MB
                    $error = "La imagen no debe exceder 5MB.";
                } else {
                    $upload_dir = '../../assets/profile_pictures/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $file_ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
                    $file_name = 'padre_' . $id_padre . '_' . time() . '.' . $file_ext;
                    $file_path = $upload_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $file_path)) {
                        $foto_path = 'padre_' . $id_padre . '_' . time() . '.' . $file_ext;
                    } else {
                        $error = "Error al cargar la imagen.";
                    }
                }
            }
            
            if (!$error) {
                $sql_update = "UPDATE padres SET nombre = :nombre, apellido = :apellido, telefono = :telefono, correo = :correo, foto = :foto 
                              WHERE id_padre = :id_padre";
                $stmt_update = $conexion->prepare($sql_update);
                $stmt_update->execute([
                    ':nombre' => $nombre,
                    ':apellido' => $apellido,
                    ':telefono' => $telefono,
                    ':correo' => $correo,
                    ':foto' => $foto_path,
                    ':id_padre' => $id_padre
                ]);
                
                $mensaje = "Perfil actualizado exitosamente.";
                $info_padre = [
                    'nombre' => $nombre,
                    'apellido' => $apellido,
                    'telefono' => $telefono,
                    'correo' => $correo,
                    'foto' => $foto_path
                ];
            }
        } catch (Exception $e) {
            $error = "Error al actualizar: " . $e->getMessage();
        }
    }
}

// Obtener hijos
$sql_hijos = "SELECT a.id_alumno, a.nombre, a.apellido, a.correo
              FROM padres_alumnos pa
              JOIN alumnos a ON pa.ID_alumno = a.id_alumno
              WHERE pa.padres_idpadres = :id_padre
              ORDER BY a.nombre";
$stmt_hijos = $conexion->prepare($sql_hijos);
$stmt_hijos->execute([':id_padre' => $id_padre]);
$hijos = $stmt_hijos->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="page-content">
    <div class="container-fluid max-width-800">
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="primary-dark-strong">Mi Perfil</h1>
            </div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success"><?php echo $mensaje; ?></div>
        <?php endif; ?>

        <!-- Formulario de Perfil -->
        <div class="card p-4 mb-4">
            <h4 class="primary-dark-strong section-title">Información Personal</h4>
            
            <form method="POST" enctype="multipart/form-data">
                <!-- Foto de Perfil -->
                <div class="row mb-4">
                    <div class="col-md-3 text-center">
                            <?php 
                            $foto_url = $info_padre['foto'] ? '../../assets/profile_pictures/' . $info_padre['foto'] : '../../assets/imgs/logo.png';
                            ?>
                            <img src="<?php echo htmlspecialchars($foto_url); ?>" 
                                alt="Foto de perfil" 
                                class="profile-img-150">
                        <div class="mt-3">
                            <input type="file" name="foto" accept="image/*" class="form-control" id="foto_input">
                            <small class="text-muted">JPG, PNG o GIF (máx 5MB)</small>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-600">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($info_padre['nombre']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label font-600">Apellido</label>
                                <input type="text" name="apellido" class="form-control" value="<?php echo htmlspecialchars($info_padre['apellido']); ?>" required>
                            </div>
                        </div>

                            <div class="mb-3">
                                <label class="form-label font-600">Email</label>
                                <input type="email" name="correo" class="form-control" value="<?php echo htmlspecialchars($info_padre['correo']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label font-600">Teléfono</label>
                                <input type="tel" name="telefono" class="form-control" value="<?php echo htmlspecialchars($info_padre['telefono'] ?? ''); ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-strong-padding">
                    Guardar Cambios
                </button>
            </form>
        </div>

        <!-- Información de Hijos -->
        <div class="card p-4">
            <h4 class="primary-dark-strong section-title">Mis Hijos</h4>
            
            <?php if (count($hijos) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="thead-primary-light">
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($hijos as $hijo): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($hijo['nombre'] . ' ' . $hijo['apellido']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($hijo['correo']); ?></td>
                                    <td>
                                        <a href="index.php?alumno=<?php echo $hijo['id_alumno']; ?>" class="btn btn-sm btn-primary">
                                            Ver Calificaciones
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No tiene hijos registrados en el sistema.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../Includes/footer.php'; ?>
